<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use SplQueue;

class PrioritySorter
{
    private $errors = [];

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    private function createQueue(array $values)
    {
        $queue = new SplQueue();
        array_map([$queue, 'push'], $values);

        return $queue;
    }

    public function sort(TestSuite $suite)
    {
        $this->sortTestSuite($suite);
    }

    private function sortTestSuite(TestSuite $suite)
    {
        //$suite = TestSuiteDecorator::decorate($suite);

        $tests = $suite->tests();
        $orderedTests = $this->createQueue($tests);

        $areTestsReordered = false;
        foreach ($tests as $position => $test) {
            if ($this->sortTest($test, $position, $orderedTests)) {
                $areTestsReordered = true;
            }
        }

        $groups = $suite->getGroupDetails();
        $areGroupsReordered = false;
        foreach ($groups as $groupName => $group) {

            $isGroupReordered = false;
            $orderedGroup = $this->createQueue($group);
            foreach ($group as $position => $test) {
                if ($this->sortTest($test, $position, $orderedGroup)) {
                    $isGroupReordered = true;
                }
            }

            if ($isGroupReordered) {
                $groups[$groupName] = iterator_to_array($orderedGroup);
                $areGroupsReordered = true;
            }
        }

        if ($areTestsReordered) {
            $suite->setTests(iterator_to_array($orderedTests));
        }

        if ($areGroupsReordered) {
            $suite->setGroupDetails($groups);
        }

        return $areTestsReordered || $areGroupsReordered;
    }

    private function sortTest($test, $position, SplQueue $orderedTests)
    {
        if (($test instanceof TestSuite && $this->sortTestSuite($test)) ||
            ($test instanceof TestCase &&
                !TestCaseDecorator::decorate($test)->hasDependencies() &&
                $this->isError($test)
            )
        ) {

            unset($orderedTests[$position]);
            $orderedTests->unshift($test);

            return true;
        }

        return false;
    }

    private function isError(TestCase $test)
    {
        return in_array(['class' => get_class($test), 'test' => $test->getName()], $this->errors);
    }
}
