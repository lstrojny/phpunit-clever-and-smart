<?php
namespace PHPUnit\Runner\CleverAndSmart\Storage;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit\Runner\CleverAndSmart\Run;

interface StorageInterface
{
    const STATUS_PASSED      = 0;
    const STATUS_SKIPPED     = 1;
    const STATUS_INCOMPLETE  = 2;
    const STATUS_FAILURE     = 3;
    const STATUS_ERROR       = 4;
    const STATUS_FATAL_ERROR = 5;
    const STATUS_CANCEL      = 6;

    /**
     * Record a test run
     *
     * @param Run $run
     * @param TestCase $test
     * @param float $time
     * @param integer $status
     * @return void
     */
    public function record(Run $run, TestCase $test, $time, $status);

    /**
     * Get recorded test errors
     *
     * @param array $results
     * @return array
     */
    public function getRecordings(array $results);
}
