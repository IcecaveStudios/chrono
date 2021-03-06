<?php

namespace Icecave\Chrono\Iterator;

use Icecave\Chrono\Date;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\TimeSpan\Period;
use PHPUnit\Framework\TestCase;

class TimeSpanIteratorTest extends TestCase
{
    public function setUp(): void
    {
        $this->startTime = new Date(2012, 12, 10);
        $this->timeSpan  = new Period(0, 0, 1);

        $this->expected = [
            0 => new DateTime(2012, 12, 10),
            1 => new DateTime(2012, 12, 11),
            2 => new DateTime(2012, 12, 12),
            3 => new DateTime(2012, 12, 13),
            4 => new DateTime(2012, 12, 14),
        ];
    }

    public function testIteration()
    {
        $iterator = new TimeSpanIterator($this->startTime, $this->timeSpan, 5);

        $result = [];
        foreach ($iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertEquals($this->expected, $result);
    }

    public function testIterationUnlimited()
    {
        $iterator = new TimeSpanIterator($this->startTime, $this->timeSpan, null);

        $result = [];
        foreach ($iterator as $index => $value) {
            $result[$index] = $value;

            if ($index >= 4) {
                break;
            }
        }

        $this->assertEquals($this->expected, $result);
    }
}
