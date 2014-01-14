<?php
namespace PHPUnit\Runner\CleverAndSmart;

use SplPriorityQueue as BasePriorityQueue;

class PriorityQueue extends BasePriorityQueue
{
    public function compare($left, $right)
    {
        return strcmp($right, $left);
    }
}
