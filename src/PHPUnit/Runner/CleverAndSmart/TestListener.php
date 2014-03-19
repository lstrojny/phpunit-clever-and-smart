<?php
namespace PHPUnit\Runner\CleverAndSmart;

use PHPUnit\Runner\CleverAndSmart\Storage\StorageInterface;
use PHPUnit_Framework_TestListener as TestListenerInterface;
use PHPUnit_Framework_Test as Test;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_TestSuite as TestSuite;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;
use Exception;
use PHPUnit_Runner_BaseTestRunner as TestRunner;

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
        $this->storage->record($this->run, $test, $time, StorageInterface::STATUS_ERROR);
    }

    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->storage->record($this->run, $test, $time, StorageInterface::STATUS_ERROR);
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

        $this->sort($suite);

        register_shutdown_function(array($this, 'onFatalError'));
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, array($this, 'onCancel'));
        }
    }

    private function sort(TestSuite $suite)
    {
        $sorter = new PrioritySorter(
            $this->storage->getRecordings(
                array(
                    StorageInterface::STATUS_ERROR,
                    StorageInterface::STATUS_FAILURE,
                    StorageInterface::STATUS_CANCEL,
                    StorageInterface::STATUS_FATAL_ERROR,
                    StorageInterface::STATUS_SKIPPED,
                    StorageInterface::STATUS_INCOMPLETE,
                ),
                false
            ),
            $this->storage->getRecordings(
                array(
                    StorageInterface::STATUS_PASSED,
                )
            )
        );
        $sorter->sort($suite);
    }

    public function startTest(Test $test)
    {
        $this->currentTest = $test;
    }

    public function endTest(Test $test, $time)
    {
        $this->currentTest = null;
        if ($test instanceof TestCase && $test->getStatus() === TestRunner::STATUS_PASSED) {
            $this->storage->record($this->run, $test, $time, StorageInterface::STATUS_PASSED);
        }
    }

    public function addIncompleteTest(Test $test, Exception $e, $time)
    {
        $this->storage->record($this->run, $test, $time, StorageInterface::STATUS_INCOMPLETE);
    }

    public function addSkippedTest(Test $test, Exception $e, $time)
    {
        $this->storage->record($this->run, $test, $time, StorageInterface::STATUS_SKIPPED);
    }

    public function endTestSuite(TestSuite $suite)
    {
    }

    public function onFatalError()
    {
        $error = error_get_last();
        if (!$error || $error['type'] !== E_ERROR || !$this->currentTest) {
            return;
        }

        $this->storage->record($this->run, $this->currentTest, 0, StorageInterface::STATUS_FATAL_ERROR);
    }

    public function onCancel()
    {
        if (!$this->currentTest) {
            return;
        }

        $this->storage->record($this->run, $this->currentTest, 0, StorageInterface::STATUS_CANCEL);

        exit(1);
    }
}
