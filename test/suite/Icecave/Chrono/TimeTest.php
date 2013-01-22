<?php
namespace Icecave\Chrono;

use Phake;
use PHPUnit_Framework_TestCase;

class TimeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_time = new Time(10, 20, 30);
    }

    public function testNormalization()
    {
        $time = new Time(10, 20, 70);
        $this->assertSame('10:21:10+00:00', $time->isoString());
    }

    public function testHours()
    {
        $this->assertSame(10, $this->_time->hours());
    }

    public function testMinutes()
    {
        $this->assertSame(20, $this->_time->minutes());
    }

    public function testSeconds()
    {
        $this->assertSame(30, $this->_time->seconds());
    }

    public function testToTimeZone()
    {
        $timeZone = new TimeZone(36000);
        $result = $this->_time->toTimeZone($timeZone);

        $this->assertInstanceOf(__NAMESPACE__ . '\Time', $result);
        $this->assertSame('20:20:30+10:00', $result->isoString());
    }

    public function testToTimeZoneSame()
    {
        $result = $this->_time->toTimeZone(new TimeZone);
        $this->assertSame($this->_time, $result);
    }

    public function testToUtc()
    {
        $timeZone = new TimeZone(36000);
        $time = new Time(10, 20, 30, $timeZone);
        $result = $time->toUtc();

        $this->assertInstanceOf(__NAMESPACE__ . '\Time', $result);
        $this->assertSame('00:20:30+00:00', $result->isoString());
    }

    public function testTimeZone()
    {
        $this->assertTrue($this->_time->timeZone()->isUtc());

        $timeZone = new TimeZone(36000, true);
        $time = new Time(10, 20, 30, $timeZone);
        $this->assertSame($timeZone, $time->timeZone());
    }

    public function testCompareSelf()
    {
        $this->assertSame(0, $this->_time->compare($this->_time));
    }

    public function testCompareClone()
    {
        $time = clone $this->_time;
        $this->assertSame(0, $this->_time->compare($time));
    }

    public function testCompareTime()
    {
        $time = new Time(10, 20, 31);
        $this->assertLessThan(0, $this->_time->compare($time));
        $this->assertGreaterThan(0, $time->compare($this->_time));

        $time = new Time(10, 21, 30);
        $this->assertLessThan(0, $this->_time->compare($time));
        $this->assertGreaterThan(0, $time->compare($this->_time));

        $time = new Time(11, 20, 30);
        $this->assertLessThan(0, $this->_time->compare($time));
        $this->assertGreaterThan(0, $time->compare($this->_time));
    }

    public function testCompareTimeZone()
    {
        $time = new Time(10, 20, 30, new TimeZone(36000));
        $this->assertLessThan(0, $this->_time->compare($time));
        $this->assertGreaterThan(0, $time->compare($this->_time));
    }

    public function testIsoString()
    {
        $this->assertEquals('10:20:30+00:00', $this->_time->isoString());
        $this->assertEquals('10:20:30+00:00', $this->_time->__toString());
    }
}
