<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;

/** @group grp */
class NestedTest extends TestCase
{
    public function testSuccess()
    {
        usleep(1000);
        $this->assertTrue(PHPUNIT_RUNNER_CLEVERANDSMART_SUCCESS);
    }

    public function testFailure()
    {
        usleep(2000);
        $this->assertFalse(PHPUNIT_RUNNER_CLEVERANDSMART_FAILURE);
    }

    public function testError()
    {
        usleep(3000);
        if (PHPUNIT_RUNNER_CLEVERANDSMART_ERROR) {
            throw new \Exception();
        }
        $this->assertTrue(true);
    }
}
