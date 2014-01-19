<?php
namespace PHPUnit\Runner\CleverAndSmart\Unit\Storage;

use PHPUnit\Runner\CleverAndSmart\Run;
use PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage;
use PHPUnit\Runner\CleverAndSmart\Storage\StorageInterface;
use PHPUnit_Framework_TestCase as TestCase;
use ReflectionClass;

class Test extends TestCase
{
}

class Sqlite3StorageTest extends TestCase
{
    /** @var string */
    private $file;

    /** @var Sqlite3Storage */
    private $storage;

    /** @var TestCase */
    private $test1;

    /** @var TestCase */
    private $test2;

    /** @var Run */
    private $run1;

    /** @var Run */
    private $run2;

    public function setUp()
    {
        $this->file = __DIR__ . '/.phpunit-cas-test.db';
        $this->reset();

        $this->storage = new Sqlite3Storage($this->file);
        $this->test1 = new Test();
        $this->test1->setName('testMethod1');
        $this->test2 = new Test();
        $this->test2->setName('testMethod2');
        $this->run1 = new Run();
        $this->run2 = new Run();
    }

    public function tearDown()
    {
        $this->reset();
    }

    private function reset()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public static function getTypes()
    {
        $class = new ReflectionClass('PHPUnit\Runner\CleverAndSmart\Storage\StorageInterface');

        $types = array();
        foreach ($class->getConstants() as $name => $value) {
            $types[] = array($value, $name);
        }

        return $types;
    }

    public static function getErrorTypes()
    {
        return array(
            array(StorageInterface::STATUS_FAILURE, 'STATUS_FAILURE'),
            array(StorageInterface::STATUS_ERROR, 'STATUS_ERROR'),
            array(StorageInterface::STATUS_FATAL_ERROR, 'STATUS_FATAL_ERROR'),
            array(StorageInterface::STATUS_CANCEL, 'STATUS_CANCEL'),
        );
    }

    /** @dataProvider getTypes */
    public function testNormalRecording($type)
    {
        $this->assertEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 1000, $type);
        $this->storage->record($this->run1, $this->test1, 2000, $type);
        $recordings = $this->storage->getRecordings(array($type));
        $this->assertNotEmpty($recordings);

        $this->assertCount(3, $recordings[0]);
        $this->assertArrayHasKey('class', $recordings[0]);
        $this->assertArrayHasKey('test', $recordings[0]);
        $this->assertArrayHasKey('time', $recordings[0]);
        $this->assertSame(1500.0, $recordings[0]['time']);

        $recordings = $this->storage->getRecordings(array($type), false);
        $this->assertCount(2, $recordings[0]);
        $this->assertArrayHasKey('class', $recordings[0]);
        $this->assertArrayHasKey('test', $recordings[0]);
    }

    /** @dataProvider getTypes */
    public function testRecordingsAreSortedByFrequency($type)
    {
        $this->assertEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 0, $type);
        $this->storage->record($this->run1, $this->test2, 0, $type);
        $this->storage->record($this->run1, $this->test1, 0, $type);
        $this->storage->record($this->run1, $this->test2, 0, $type);
        $this->storage->record($this->run2, $this->test2, 0, $type);

        $this->assertSame(
            array(
                array('class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Storage\Test', 'test' => 'testMethod2'),
                array('class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Storage\Test', 'test' => 'testMethod1'),
            ),
            $this->storage->getRecordings(array($type), false)
        );
    }

    /** @dataProvider getErrorTypes */
    public function testRecordedErrorsAreRemovedAfterFiveTimes($type)
    {
        $this->assertEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 1, $type);
        $this->assertNotEmpty($this->storage->getRecordings(array($type)));

        $this->storage->record($this->run1, $this->test1, 1, StorageInterface::STATUS_PASSED);
        $this->assertNotEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 2, StorageInterface::STATUS_PASSED);
        $this->assertNotEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 3, StorageInterface::STATUS_PASSED);
        $this->assertNotEmpty($this->storage->getRecordings(array($type)));
        $this->storage->record($this->run1, $this->test1, 4, StorageInterface::STATUS_PASSED);
        $this->assertEmpty($this->storage->getRecordings(array($type)));
    }
}
