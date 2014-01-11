<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use ReflectionObject;
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
        $tests = $suite->tests();
        $queue = $this->createQueue($tests);

        /** @var $test TestCase */
        var_dump(__METHOD__);
        foreach ($tests as $test) {
            var_dump(get_class($test) . '::' . $test->getName());
        }

        $reordered = false;
        foreach ($tests as $position => $test) {
            if ($this->sortTest($test, $position, $queue)) {
                $reordered = true;
            }
        }

        $groups = Util::getInvisibleProperty($suite, 'groups');
        $reorderedGroups = false;
        foreach ($groups as $groupName => $group) {

            $reorderedGroup = false;
            $orderedGroup = $this->createQueue($group);
            foreach ($group as $position => $test) {
                if ($this->sortTest($test, $position, $orderedGroup)) {
                    $reorderedGroups = $reorderedGroup = true;
                }
            }

            if ($reorderedGroup) {
                $groups[$groupName] = iterator_to_array($orderedGroup);
            }
        }

        if ($reordered) {
            Util::setInvisibleProperty($suite, 'tests', iterator_to_array($queue));
        }

        if ($reorderedGroups) {
            Util::setInvisibleProperty($suite, 'groups', $groups);
        }

        return $reordered || $reorderedGroups;
    }

    private function sortTest($test, $position, SplQueue $testsOrdered)
    {
        if ($test instanceof TestSuite) {
            if ($this->sortTestSuite($test)) {
                unset($testsOrdered[$position]);
                $testsOrdered->unshift($test);

                return true;
            }

            return false;
        }

        if ($test instanceof TestCase && $this->isError($test)) {

            unset($testsOrdered[$position]);
            $testsOrdered->unshift($test);

            return true;
        }
    }

    private function isError(TestCase $test)
    {
        return in_array(['class' => get_class($test), 'test' => $test->getName()], $this->errors);
    }
}
