<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;

/** @group grp */
class DependentTest extends TestCase
{
    public function testSuccess()
    {
        usleep(3);
        $this->assertTrue(PHPUNIT_RUNNER_CLEVERANDSMART_SUCCESS);
    }

    /** @depends testSuccess */
    public function testFailure()
    {
        usleep(2);
        $this->assertFalse(PHPUNIT_RUNNER_CLEVERANDSMART_FAILURE);
    }

    /** @depends testFailure */
    public function testError()
    {
        usleep(1);
        if (PHPUNIT_RUNNER_CLEVERANDSMART_ERROR) {
            throw new \Exception();
        }
        $this->assertTrue(true);
    }
}
