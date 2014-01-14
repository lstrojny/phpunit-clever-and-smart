<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;

/** @group grp */
class SkipTest extends TestCase
{
    public function testSuccess1()
    {
        usleep(3000);
        $this->assertTrue(true);
    }

    public function testSkipped()
    {
        if (PHPUNIT_RUNNER_CLEVERANDSMART_SKIP) {
            $this->markTestSkipped();
        }
        $this->assertTrue(true);
        usleep(2000);
    }

    public function testIncomplete()
    {
        if (PHPUNIT_RUNNER_CLEVERANDSMART_SKIP) {
            $this->markTestIncomplete();
        }
        $this->assertTrue(true);
        usleep(1000);
    }

    public function testSuccess2()
    {
        usleep(5000);
        $this->assertTrue(true);
    }
}
