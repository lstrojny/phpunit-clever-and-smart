<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use ReflectionObject;

class PrioritySorter
{
    private $errors = [];

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function sort(TestSuite $suite)
    {
        $tests = $testsOrdered = $suite->tests();

        $reorderPosition = -1;
        foreach ($tests as $position => $test) {
            $this->sortTest($test, $position, $testsOrdered, $reorderPosition);
        }

        $groupReorderPosition = -1;
        $groups = Util::getInvisibleProperty($suite, 'groups');
        foreach ($groups as $groupName => $group) {
            $orderedGroup = $group;
            $groupReorderPosition = -1;
            foreach ($group as $position => $test) {
                $this->sortTest($test, $position, $orderedGroup, $groupReorderPosition);
            }

            if ($groupReorderPosition < -1) {
                ksort($orderedGroup);
                $groups[$groupName] = $orderedGroup;
            }
        }

        if ($reorderPosition < -1 || $groupReorderPosition < -1) {
            ksort($testsOrdered);

            Util::setInvisibleProperty($suite, 'tests', $testsOrdered);
            Util::setInvisibleProperty($suite, 'groups', $groups);
        }
    }

    private function sortTest($test, $position, array &$testsOrdered, &$reorderPosition)
    {
        if ($test instanceof TestSuite) {
            $this->sort($test);

            return false;
        }

        if ($test instanceof TestCase && $this->isError($test)) {

            unset($testsOrdered[$position]);
            $testsOrdered[$reorderPosition--] = $test;

            return true;
        }
    }

    private function isError(TestCase $test)
    {
        return in_array(['class' => get_class($test), 'test' => $test->getName()], $this->errors);
    }
}
