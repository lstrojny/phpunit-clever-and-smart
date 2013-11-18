<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit_Framework_TestListener as TestListenerInterface;
use PHPUnit_Framework_Test as Test;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;
use Exception;
use ReflectionObject;

class TestListener implements TestListenerInterface
{
    /** @var Run */
    private $run;

    /** @var Storage */
    private $storage;

    /** @var bool */
    private $reordered = false;

    public function __construct()
    {
        $this->run = new Run();
        $this->storage = new Storage();
    }

    public function addError(Test $test, Exception $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function startTestSuite(TestSuite $suite)
    {
        if ($this->reordered) {
            return;
        }
        $this->reordered = true;

        $sorter = new PrioritySorter($this->storage->getErrors());
        $sorter->sort($suite);
    }

    public function endTest(Test $test, $time)
    {
        if ($test instanceof TestCase && $test->getStatus() === 0) {
            $this->storage->recordSuccess($this->run, $test);
        }
    }

    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
    }

    public function addSkippedTest(Test $test, Exception $e, $time)
    {
    }

    public function endTestSuite(TestSuite $suite)
    {
    }

    public function startTest(Test $test)
    {
    }
}
