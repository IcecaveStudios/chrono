<?php
namespace Icecave\Chrono\Clock;

use Icecave\Chrono\Date;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\Interval\Month;
use Icecave\Chrono\Interval\Year;
use Icecave\Chrono\TimeOfDay;
use Icecave\Chrono\TimeZone;
use PHPUnit_Framework_TestCase;
use Phake;

class AbstractClockTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->clock = Phake::partialMock(__NAMESPACE__ . '\AbstractClock');

        $this->timeZone = new TimeZone(36000, true);

        Phake::when($this->clock)
            ->currentLocalTimeInfo()
            ->thenReturn(array(10, 20, 13, 20, 11, 2013, '<unused>', '<unused>', 1, 36000));

        Phake::when($this->clock)
            ->currentUtcTimeInfo()
            ->thenReturn(array(1, 2, 3, 4, 5, 2011, '<unused>', '<unused>', 0, 0)); // Intentially set vastly different from localTimeInfo to catch potential errors.
    }

    public function verifyLocalClockSuspended()
    {
        Phake::inOrder(
            Phake::verify($this->clock, Phake::atLeast(1))->suspend(),
            Phake::verify($this->clock, Phake::times(1))->currentLocalTimeInfo(),
            Phake::verify($this->clock, Phake::atLeast(1))->resume()
        );
    }

    public function verifyUtcClockSuspended()
    {
        Phake::inOrder(
            Phake::verify($this->clock, Phake::atLeast(1))->suspend(),
            Phake::verify($this->clock, Phake::times(1))->currentUtcTimeInfo(),
            Phake::verify($this->clock, Phake::atLeast(1))->resume()
        );
    }

    public function testLocalTime()
    {
        $result = $this->clock->localTime();
        $expected = new TimeOfDay(13, 20, 10, $this->timeZone);
        $this->assertEquals($expected, $result);

        $this->verifyLocalClockSuspended();
    }

    public function testLocalDateTime()
    {
        $result = $this->clock->localDateTime();
        $expected = new DateTime(2013, 11, 20, 13, 20, 10, $this->timeZone);
        $this->assertEquals($expected, $result);

        $this->verifyLocalClockSuspended();
    }

    public function testLocalDate()
    {
        $result = $this->clock->localDate();
        $expected = new Date(2013, 11, 20, $this->timeZone);
        $this->assertEquals($expected, $result);

        $this->verifyLocalClockSuspended();
    }

    public function testLocalMonth()
    {
        $result = $this->clock->localMonth();
        $expected = new Month(new Year(2013), 11);
        $this->assertEquals($expected, $result);

        $this->verifyLocalClockSuspended();
    }

    public function testLocalYear()
    {
        $result = $this->clock->localYear();
        $expected = new Year(2013);
        $this->assertEquals($expected, $result);

        $this->verifyLocalClockSuspended();
    }

    public function testUtcTime()
    {
        $result = $this->clock->utcTime();
        $expected = new TimeOfDay(3, 2, 1);
        $this->assertEquals($expected, $result);

        $this->verifyUtcClockSuspended();
    }

    public function testUtcDateTime()
    {
        $result = $this->clock->utcDateTime();
        $expected = new DateTime(2011, 5, 4, 3, 2, 1);
        $this->assertEquals($expected, $result);

        $this->verifyUtcClockSuspended();
    }

    public function testUtcDate()
    {
        $result = $this->clock->utcDate();
        $expected = new Date(2011, 5, 4);
        $this->assertEquals($expected, $result);

        $this->verifyUtcClockSuspended();
    }

    public function testUtcMonth()
    {
        $result = $this->clock->utcMonth();
        $expected = new Month(new Year(2011), 5);
        $this->assertEquals($expected, $result);

        $this->verifyUtcClockSuspended();
    }

    public function testUtcYear()
    {
        $result = $this->clock->utcYear();
        $expected = new Year(2011);
        $this->assertEquals($expected, $result);

        $this->verifyUtcClockSuspended();
    }

    public function testSuspend()
    {
        $this->clock->suspend();

        $this->assertTrue($this->clock->isSuspended());

        // Try localDateTime twice ...
        $expected = new DateTime(2013, 11, 20, 13, 20, 10, $this->timeZone);
        $result = $this->clock->localDateTime();
        $this->assertEquals($expected, $result);

        $result = $this->clock->localDateTime();
        $this->assertEquals($expected, $result);

        // Only calls implementation once ...
        Phake::verify($this->clock, Phake::times(1))->currentLocalTimeInfo();

        // Try utcDateTime twice ...
        $expected = new DateTime(2011, 5, 4, 3, 2, 1);
        $result = $this->clock->utcDateTime();
        $this->assertEquals($expected, $result);

        $result = $this->clock->utcDateTime();
        $this->assertEquals($expected, $result);

        // Only calls implementation once ...
        Phake::verify($this->clock, Phake::times(1))->currentUtcTimeInfo();
    }

    public function testSuspendStacking()
    {
        $this->clock->suspend();
        $this->assertTrue($this->clock->isSuspended());

        $this->clock->suspend();
        $this->assertTrue($this->clock->isSuspended());

        $this->clock->resume();
        $this->assertTrue($this->clock->isSuspended());

        $this->clock->resume();
        $this->assertFalse($this->clock->isSuspended());
    }

    public function testResumeWithLocalTime()
    {
        $this->clock->resume();

        $this->assertFalse($this->clock->isSuspended());

        // Try localDateTime twice ...
        $expected = new DateTime(2013, 11, 20, 13, 20, 10, $this->timeZone);
        $result = $this->clock->localDateTime();
        $this->assertEquals($expected, $result);

        $result = $this->clock->localDateTime();
        $this->assertEquals($expected, $result);

        // Calls implementation twice ...
        Phake::verify($this->clock, Phake::times(2))->currentLocalTimeInfo();
    }

    public function testResumeWithUtcTime()
    {
        $this->clock->resume();

        $this->assertFalse($this->clock->isSuspended());

        // Try utcDateTime twice ...
        $expected = new DateTime(2011, 5, 4, 3, 2, 1);
        $result = $this->clock->utcDateTime();
        $this->assertEquals($expected, $result);

        $result = $this->clock->utcDateTime();
        $this->assertEquals($expected, $result);

        // Calls implementation twice once ...
        Phake::verify($this->clock, Phake::times(2))->currentUtcTimeInfo();
    }
}
