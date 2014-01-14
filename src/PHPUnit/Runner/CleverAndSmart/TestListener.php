<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit\Runner\CleverAndSmart\Storage\StorageInterface;
use PHPUnit_Framework_TestListener as TestListenerInterface;
use PHPUnit_Framework_Test as Test;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;
use Exception;
use ReflectionObject;
use PHPUnit_Runner_BaseTestRunner as BaseTestRunner;

declare(ticks=1);

class TestListener implements TestListenerInterface
{
    /** @var Run */
    private $run;

    /** @var StorageInterface */
    private $storage;

    /** @var TestCase */
    private $currentTest;

    /** @var bool */
    private $reordered = false;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
        $this->run = new Run();
    }

    public function addError(Test $test, Exception $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function addRiskyTest(Test $test, Exception $e, $time)
    {
    }

    public function startTestSuite(TestSuite $suite)
    {
        if ($this->reordered) {
            return;
        }

        $this->reordered = true;
        $sorter = new PrioritySorter($this->storage->getErrors(), $this->storage->getTimings());
        $sorter->sort($suite);
        register_shutdown_function([$this, 'onFatalError']);
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, [$this, 'onCancel']);
        }
    }

    public function startTest(Test $test)
    {
        $this->currentTest = $test;
    }

    public function endTest(Test $test, $time)
    {
        $this->currentTest = null;
        if ($test instanceof TestCase && $test->getStatus() === 0) {
            $this->storage->recordSuccess($this->run, $test, $time);
        }
    }

    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function addSkippedTest(Test $test, Exception $e, $time)
    {
        $this->storage->recordError($this->run, $test);
    }

    public function endTestSuite(TestSuite $suite)
    {
    }

    public function onFatalError()
    {
        $error = error_get_last();
        if (!$error || $error['type'] !== E_ERROR) {
            return;
        }

        $this->storage->recordError($this->run, $this->currentTest);
    }

    public function onCancel()
    {
        if ($this->currentTest) {
            $this->storage->recordError($this->run, $this->currentTest);
        }

        exit(255);
    }
}
