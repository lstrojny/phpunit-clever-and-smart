<?php
namespace PHPUnit\Tests\Runner\CleverAndSmart\Integration;

use PHPUnit_Framework_TestCase as TestCase;

/** @group grp */
class DataTest extends TestCase
{
    public static function provideData()
    {
        return [
            ['PHPUNIT_RUNNER_CLEVERANDSMART_FAILURE'],
            ['PHPUNIT_RUNNER_CLEVERANDSMART_ERROR'],
            ['PHPUNIT_RUNNER_CLEVERANDSMART_SUCCESS'],
        ];
    }

    /** @dataProvider provideData */
    public function testData($constant)
    {
        switch ($constant) {
            case 'PHPUNIT_RUNNER_CLEVERANDSMART_FAILURE':
                $this->assertFalse(PHPUNIT_RUNNER_CLEVERANDSMART_FAILURE);
                break;

            case 'PHPUNIT_RUNNER_CLEVERANDSMART_ERROR':
                if (PHPUNIT_RUNNER_CLEVERANDSMART_ERROR) {
                    throw new \Exception('ERROR');
                }
                $this->assertTrue(true);
                break;

            case PHPUNIT_RUNNER_CLEVERANDSMART_SUCCESS:
                $this->assertTrue(PHPUNIT_RUNNER_CLEVERANDSMART_SUCCESS);
                break;
        }
    }
}
