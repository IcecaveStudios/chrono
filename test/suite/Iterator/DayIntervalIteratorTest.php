<?php
namespace Icecave\Chrono\Iterator;

use Icecave\Chrono\Date;
use Icecave\Chrono\Interval\Interval;
use PHPUnit_Framework_TestCase;

class DayIntervalIteratorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->startTime = new Date(2010, 12, 20);
        $this->endTime   = new Date(2010, 12, 25);

        $this->interval = new Interval($this->startTime, $this->endTime);

        $this->expected = array(
            0 => new Date(2010, 12, 20),
            1 => new Date(2010, 12, 21),
            2 => new Date(2010, 12, 22),
            3 => new Date(2010, 12, 23),
            4 => new Date(2010, 12, 24),
        );
    }

    public function testIteration()
    {
        $iterator = new DayIntervalIterator($this->interval);

        $this->assertEquals($this->expected, iterator_to_array($iterator, true));
    }
}
