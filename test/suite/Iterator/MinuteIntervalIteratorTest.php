<?php

namespace Icecave\Chrono\Iterator;

use Icecave\Chrono\DateTime;
use Icecave\Chrono\Interval\Interval;
use PHPUnit\Framework\TestCase;

class MinuteIntervalIteratorTest extends TestCase
{
    public function setUp(): void
    {
        $this->startTime = new DateTime(2010, 12, 25, 10, 20, 30);
        $this->endTime   = new DateTime(2010, 12, 25, 10, 25, 30);

        $this->interval = new Interval($this->startTime, $this->endTime);

        $this->expected = [
            0 => new DateTime(2010, 12, 25, 10, 20, 30),
            1 => new DateTime(2010, 12, 25, 10, 21, 30),
            2 => new DateTime(2010, 12, 25, 10, 22, 30),
            3 => new DateTime(2010, 12, 25, 10, 23, 30),
            4 => new DateTime(2010, 12, 25, 10, 24, 30),
        ];
    }

    public function testIteration()
    {
        $iterator = new MinuteIntervalIterator($this->interval);

        $this->assertEquals($this->expected, iterator_to_array($iterator, true));
    }
}
