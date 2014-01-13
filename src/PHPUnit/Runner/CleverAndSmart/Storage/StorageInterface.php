<?php
namespace PHPUnit\Runner\CleverAndSmart\Storage;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit\Runner\CleverAndSmart\Run;

interface StorageInterface
{
    /**
     * Record a test error
     *
     * @param Run $run
     * @param TestCase $test
     * @return void
     */
    public function recordError(Run $run, TestCase $test);

    /**
     * Record a test failure
     *
     * @param Run $run
     * @param TestCase $test
     * @param float $time
     * @return void
     */
    public function recordSuccess(Run $run, TestCase $test, $time);

    /**
     * Get recorded test errors
     *
     * @return array
     */
    public function getErrors();

    /**
     * @return array
     */
    public function getTimings();
}
