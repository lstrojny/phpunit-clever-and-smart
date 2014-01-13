<?php
namespace PHPUnit\Runner\CleverAndSmart;

use ArrayIterator;
use IteratorAggregate;
use SplPriorityQueue;
use SplQueue;

class SegmentedQueue implements IteratorAggregate
{
    /** @var SplQueue */
    public $unknown;

    /** @var SplQueue */
    public $errors;

    /** @var SplPriorityQueue */
    public $timed;

    public function __construct(array $values = [])
    {
        $this->unknown = new SplQueue();
        array_map([$this->unknown, 'push'], $values);
        $this->errors = new SplQueue();
        $this->timed = new SplPriorityQueue();
    }

    public function getIterator()
    {
        return new ArrayIterator(
            array_values(
                array_filter(
                    array_merge(
                        iterator_to_array($this->errors),
                        iterator_to_array($this->unknown),
                        iterator_to_array($this->timed)
                    )
                )
            )
        );
    }
}
