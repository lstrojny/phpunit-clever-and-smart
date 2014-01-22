<?php
namespace PHPUnit\Runner\CleverAndSmart\Storage;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit\Runner\CleverAndSmart\Run;

class MockedStorage implements StorageInterface
{
    public function __construct()
    {
        // nothing todo
    }

    public function record(Run $run, TestCase $test, $time, $status)
    {
        // nothing todo
    }

    public function getRecordings(array $types, $includeTime = true)
    {
        return array();
    }
}
