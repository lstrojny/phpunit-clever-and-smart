<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;

class PrioritySorter
{
    const SORT_NONE = 0;
    const SORT_TIMING = 1;
    const SORT_ERROR = 2;

    private $errors = array();

    private $timings = array();

    public function __construct(array $errors, array $timings = array())
    {
        $this->errors = $errors;
        $this->timings = $timings;
    }

    public function sort(TestSuite $suite)
    {
        $this->sortTestSuite($suite);
    }

    private function sortTestSuite(TestSuite $suite)
    {
        $tests = $suite->tests();
        $orderedTests = new SegmentedQueue($tests);

        $testsOrderResult = array(static::SORT_NONE, null);

        foreach ($tests as $test) {
            if ($test instanceof TestCase && Util::getInvisibleProperty($test, 'dependencies', 'hasDependencies')) {
                return $testsOrderResult;
            }
        }

        foreach ($tests as $position => $test) {
            list($testOrderResult, $time) = $this->sortTest($test, $position, $orderedTests);
            if ($testsOrderResult[0] < $testOrderResult) {
                $testsOrderResult = array($testOrderResult, $time);
            }
        }

        $groups = Util::getInvisibleProperty($suite, 'groups', 'getGroupDetails');
        $groupsOrderResult = array(static::SORT_NONE, null);
        foreach ($groups as $groupName => $group) {

            $groupOrderResult = array(static::SORT_NONE, null);
            $orderedGroup = new SegmentedQueue($group);
            foreach ($group as $position => $test) {
                list($testOrderResult, $time) = $this->sortTest($test, $position, $orderedGroup);
                if ($groupOrderResult[0] < $testOrderResult) {
                    $groupOrderResult = array($testOrderResult, $time);
                }
            }

            if ($groupOrderResult[0] > static::SORT_NONE) {
                $groups[$groupName] = iterator_to_array($orderedGroup);

                if ($groupsOrderResult[0] < $groupOrderResult[0]) {
                    $groupsOrderResult = $groupOrderResult;
                }
            }
        }

        if ($testsOrderResult[0] > static::SORT_NONE) {
            Util::setInvisibleProperty($suite, 'tests', iterator_to_array($orderedTests), 'setTests');
        }

        if ($groupsOrderResult) {
            Util::setInvisibleProperty($suite, 'groups', $groups, 'setGroupDetails');
        }

        return $testsOrderResult[0] > $groupsOrderResult[0] ? $testsOrderResult : $groupsOrderResult;
    }

    private function sortTest($test, $position, SegmentedQueue $orderedTests)
    {
        if ($test instanceof TestSuite) {

            list($result, $time) = $this->sortTestSuite($test);

            if ($result === static::SORT_ERROR) {

                $orderedTests->unknown[$position] = null;
                $orderedTests->errors->push($test);

            } elseif ($result === static::SORT_TIMING) {

                $orderedTests->unknown[$position] = null;
                $orderedTests->timed->insert($test, $time);

            }

            return array($result, $time);
        }

        if ($test instanceof TestCase) {

            if ($this->isError($test)) {

                $orderedTests->unknown[$position] = null;
                $orderedTests->errors->push($test);

                return array(static::SORT_ERROR, null);
            }

            if ($time = $this->getTime($test)) {

                $orderedTests->unknown[$position] = null;
                $orderedTests->timed->insert($test, $time);

                return array(static::SORT_TIMING, $time);
            }
        }

        return array(static::SORT_NONE, null);
    }

    private function getTime(TestCase $test)
    {
        $name = $test->getName();
        $class = get_class($test);

        foreach ($this->timings as $timing) {
            if ($timing['class'] === $class && $timing['test'] === $name) {
                return $timing['time'];
            }
        }
    }

    private function isError(TestCase $test)
    {
        return in_array(array('class' => get_class($test), 'test' => $test->getName()), $this->errors);
    }
}
