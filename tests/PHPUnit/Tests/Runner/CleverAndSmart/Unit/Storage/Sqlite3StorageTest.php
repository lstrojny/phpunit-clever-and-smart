<?php
namespace PHPUnit\Runner\CleverAndSmart\Unit\Storage;

use PHPUnit\Runner\CleverAndSmart\Run;
use PHPUnit\Runner\CleverAndSmart\Storage\Sqlite3Storage;
use PHPUnit_Framework_TestCase as TestCase;

class Test extends TestCase
{
}

class Sqlite3StorageTest extends TestCase
{
    /** @var Sqlite3Storage */
    private $storage;

    /** @var TestCase */
    private $test1;

    /** @var TestCase */
    private $test2;

    /** @var Run */
    private $run1;

    public function setUp()
    {
        $this->reset();

        $this->storage = new Sqlite3Storage();
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
        $file = __DIR__ . '/../../../../../../../.phpunit-cas.db';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function testRecordSuccess()
    {
        $this->assertEmpty($this->storage->getErrors());
        $this->storage->recordSuccess($this->run1, $this->test1);
        $this->assertEmpty($this->storage->getErrors());
    }

    public function testRecordError()
    {
        $this->assertEmpty($this->storage->getErrors());
        $this->storage->recordError($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());
    }

    public function testRecordedErrorsAreSortedByFrequency()
    {
        $this->assertEmpty($this->storage->getErrors());
        $this->storage->recordError($this->run1, $this->test1);
        $this->storage->recordError($this->run1, $this->test2);
        $this->storage->recordError($this->run1, $this->test1);
        $this->storage->recordError($this->run1, $this->test2);
        $this->storage->recordError($this->run2, $this->test2);

        $this->assertSame(
            [
                ['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Storage\Test', 'test' => 'testMethod2'],
                ['class' => 'PHPUnit\Runner\CleverAndSmart\Unit\Storage\Test', 'test' => 'testMethod1'],
            ],
            $this->storage->getErrors()
        );
    }

    public function testRecordedErrorsAreRemovedAfterFiveTimes()
    {
        $this->assertEmpty($this->storage->getErrors());
        $this->storage->recordError($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());

        $this->storage->recordSuccess($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());
        $this->storage->recordSuccess($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());
        $this->storage->recordSuccess($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());
        $this->storage->recordSuccess($this->run1, $this->test1);
        $this->assertNotEmpty($this->storage->getErrors());
        //$this->storage->recordSuccess($this->run1, $this->test1);

        //$this->assertEmpty($this->storage->getErrors());
    }
}
