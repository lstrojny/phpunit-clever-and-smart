<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;

/** @group grp */
class FatalErrorTest extends TestCase
{
    public function testSuccess1()
    {
        usleep(1000);
        $this->assertTrue(true);
    }

    public function testFatal()
    {
        if (PHPUNIT_RUNNER_CLEVERANDSMART_FATAL) {
            $this->invalidMethod();
        }
        $this->assertTrue(true);
    }

    public function testSuccess2()
    {
        usleep(2000);
        $this->assertTrue(true);
    }
}
