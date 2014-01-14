<?php
namespace PHPUnit\Runner\CleverAndSmart\Unit;

use PHPUnit\Runner\CleverAndSmart\SegmentedQueue;
use PHPUnit_Framework_TestCase as TestCase;

class SegmentedQueueTest extends TestCase
{
    /** @var SegmentedQueue */
    private $queue;

    public function setUp()
    {
        $this->queue = new SegmentedQueue();
    }

    public function testPushToUnknownQueue()
    {
        $this->queue->unknown->push('test1');
        $this->queue->unknown->push('test2');

        $this->assertSame(['test1', 'test2'], iterator_to_array($this->queue));
    }

    public function testPushWithPriority()
    {
        $this->queue->unknown->push('test1');
        $this->queue->timed->insert('test4', 0.1);
        $this->queue->timed->insert('test5', 0.2);
        $this->queue->timed->insert('test3', 0.05);
        $this->queue->unknown->push('test2');

        $this->assertSame(['test1', 'test2', 'test3', 'test4', 'test5'], iterator_to_array($this->queue));
    }

    public function testResetWithNull()
    {
        $this->queue->unknown->push('test1');
        $this->queue->unknown->push('test2');
        $this->queue->unknown[0] = null;

        $this->assertSame(['test2'], iterator_to_array($this->queue));
    }
}
