<?php

namespace Icecave\Chrono\TimeSpan;

use DateInterval;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\TimeZone;
use Phake;
use PHPUnit\Framework\TestCase;

/**
 * @covers Icecave\Chrono\TimeSpan\Duration
 * @covers Icecave\Chrono\Detail\Iso8601
 */
class DurationTest extends TestCase
{
    public function setUp(): void
    {
        $this->duration = Duration::fromComponents(1, 2, 3, 4, 5);

        $this->before = Duration::fromComponents(1, 2, 3, 4, 4);
        $this->same   = Duration::fromComponents(1, 2, 3, 4, 5);
        $this->after  = Duration::fromComponents(1, 2, 3, 4, 6);
    }

    public function testStaticFactoryMethods()
    {
        $this->assertSame(694861, Duration::fromComponents(1, 1, 1, 1, 1)->totalSeconds());
        $this->assertSame(604800, Duration::fromWeeks(1)->totalSeconds());
        $this->assertSame(86400, Duration::fromDays(1)->totalSeconds());
        $this->assertSame(3600, Duration::fromHours(1)->totalSeconds());
        $this->assertSame(60, Duration::fromMinutes(1)->totalSeconds());
        $this->assertSame(1, Duration::fromSeconds(1)->totalSeconds());
    }

    public function testWeeks()
    {
        $this->assertSame(1, $this->duration->weeks());
    }

    public function testDays()
    {
        $this->assertSame(2, $this->duration->days());
    }

    public function testHours()
    {
        $this->assertSame(3, $this->duration->hours());
    }

    public function testMinutes()
    {
        $this->assertSame(4, $this->duration->minutes());
    }

    public function testSeconds()
    {
        $this->assertSame(5, $this->duration->seconds());
    }

    public function testTotalDays()
    {
        $this->assertSame(9, $this->duration->totalDays());
    }

    public function testTotalHours()
    {
        $this->assertSame(219, $this->duration->totalHours());
    }

    public function testTotalMinutes()
    {
        $this->assertSame(13144, $this->duration->totalMinutes());
    }

    public function testTotalSeconds()
    {
        $this->assertSame(788645, $this->duration->totalSeconds());
    }

    public function testIsEmpty()
    {
        $this->assertFalse($this->duration->isEmpty());

        $duration = new Duration();

        $this->assertTrue($duration->isEmpty());
    }

    public function testCompareWithNotComparableException()
    {
        $this->expectException('Icecave\Parity\Exception\NotComparableException');
        $this->duration->compare('foo');
    }

    public function testCompare()
    {
        $this->assertGreaterThan(0, $this->duration->compare($this->before));
        $this->assertSame(0, $this->duration->compare($this->same));
        $this->assertLessThan(0, $this->duration->compare($this->after));
    }

    public function testIsEqualTo()
    {
        $this->assertFalse($this->duration->isEqualTo($this->before));
        $this->assertTrue($this->duration->isEqualTo($this->same));
        $this->assertFalse($this->duration->isEqualTo($this->after));
    }

    public function testIsNotEqualTo()
    {
        $this->assertTrue($this->duration->isNotEqualTo($this->before));
        $this->assertFalse($this->duration->isNotEqualTo($this->same));
        $this->assertTrue($this->duration->isNotEqualTo($this->after));
    }

    public function testIsGreaterThan()
    {
        $this->assertTrue($this->duration->isGreaterThan($this->before));
        $this->assertFalse($this->duration->isGreaterThan($this->same));
        $this->assertFalse($this->duration->isGreaterThan($this->after));
    }

    public function testIsLessThan()
    {
        $this->assertFalse($this->duration->isLessThan($this->before));
        $this->assertFalse($this->duration->isLessThan($this->same));
        $this->assertTrue($this->duration->isLessThan($this->after));
    }

    public function testIsGreaterThanOrEqualTo()
    {
        $this->assertTrue($this->duration->isGreaterThanOrEqualTo($this->before));
        $this->assertTrue($this->duration->isGreaterThanOrEqualTo($this->same));
        $this->assertFalse($this->duration->isGreaterThanOrEqualTo($this->after));
    }

    public function testIsLessThanOrEqualTo()
    {
        $this->assertFalse($this->duration->isLessThanOrEqualTo($this->before));
        $this->assertTrue($this->duration->isLessThanOrEqualTo($this->same));
        $this->assertTrue($this->duration->isLessThanOrEqualTo($this->after));
    }

    public function testInverse()
    {
        $this->assertSame(-788645, $this->duration->inverse()->totalSeconds());
    }

    public function testResolveToSeconds()
    {
        $timePoint = Phake::mock('Icecave\Chrono\TimePointInterface');

        $this->assertSame(788645, $this->duration->resolveToSeconds($timePoint));

        Phake::verifyNoInteraction($timePoint);
    }

    public function testResolveToDuration()
    {
        $timePoint = Phake::mock('Icecave\Chrono\TimePointInterface');

        $this->assertSame($this->duration, $this->duration->resolveToDuration($timePoint));

        Phake::verifyNoInteraction($timePoint);
    }

    public function testResolveToPeriod()
    {
        $timePoint = Phake::mock('Icecave\Chrono\TimePointInterface');

        $result   = $this->duration->resolveToPeriod($timePoint);
        $expected = new Period(0, 0, 9, 3, 4, 5);

        $this->assertInstanceOf('Icecave\Chrono\TimeSpan\Period', $result);
        $this->assertSame(0, $expected->compare($result));

        Phake::verifyNoInteraction($timePoint);
    }

    public function testResolveToInterval()
    {
        $timeZone  = new TimeZone(36000);
        $timePoint = new DateTime(2012, 1, 2, 10, 20, 30, $timeZone);

        $result = $this->duration->resolveToInterval($timePoint);

        $this->assertInstanceOf('Icecave\Chrono\Interval\IntervalInterface', $result);
        $this->assertSame('2012-01-02T10:20:30+10:00', $result->start()->isoString());
        $this->assertSame('2012-01-11T13:24:35+10:00', $result->end()->isoString());
    }

    public function testResolveToIntervalInverse()
    {
        $duration  = new Duration(-10);
        $timePoint = new DateTime(2012, 1, 2, 0, 0, 0);

        $result = $duration->resolveToInterval($timePoint);

        $this->assertInstanceOf('Icecave\Chrono\Interval\IntervalInterface', $result);
        $this->assertSame('2012-01-01T23:59:50+00:00', $result->start()->isoString());
        $this->assertSame('2012-01-02T00:00:00+00:00', $result->end()->isoString());
    }

    public function testResolveToTimePoint()
    {
        $timeZone  = new TimeZone(36000);
        $timePoint = new DateTime(2012, 1, 2, 10, 20, 30, $timeZone);

        $result = $this->duration->resolveToTimePoint($timePoint);

        $this->assertInstanceOf('Icecave\Chrono\TimePointInterface', $result);
        $this->assertSame('2012-01-11T13:24:35+10:00', $result->isoString());
    }

    public function testNativeDateInterval()
    {
        $duration = Duration::fromIsoString('P1Y2M3DT4H5M6S');
        $native   = $duration->nativeDateInterval();

        $this->assertSame($native->format('P%dDT%hH%iM%sS'), $duration->isoString());
    }

    public function testAdd()
    {
        $duration1 = new Duration(10);
        $duration2 = new Duration(20);

        $this->assertEquals(new Duration(30), $duration1->add($duration2));
    }

    public function testAddWithInteger()
    {
        $duration1 = new Duration(10);
        $duration2 = 20;

        $this->assertEquals(new Duration(30), $duration1->add($duration2));
    }

    public function testSubtract()
    {
        $duration1 = new Duration(10);
        $duration2 = new Duration(20);

        $this->assertEquals(new Duration(-10), $duration1->subtract($duration2));
    }

    public function testSubtractWithInteger()
    {
        $duration1 = new Duration(10);
        $duration2 = 20;

        $this->assertEquals(new Duration(-10), $duration1->subtract($duration2));
    }

    public function testString()
    {
        $this->assertSame('1w 2d 03:04:05', $this->duration->string());
    }

    public function testIsoString()
    {
        $this->assertSame('P9DT3H4M5S', $this->duration->isoString());
        $this->assertSame('P9DT3H4M5S', $this->duration->__toString());
    }

    /**
     * @dataProvider validIsoStrings
     */
    public function testFromIsoString($isoString, $expected)
    {
        $result = Duration::fromIsoString($isoString);
        $this->assertSame($expected, $result->isoString());
    }

    public function validIsoStrings()
    {
        return [
            // Duration Format - Empty/Zero
            'Zero weeks'                                => ['P0W',                     'PT0S'],
            'Zero years'                                => ['P0Y',                     'PT0S'],
            'Zero months'                               => ['P0M',                     'PT0S'],
            'Zero days'                                 => ['P0D',                     'PT0S'],
            'Zero hours'                                => ['PT0H',                    'PT0S'],
            'Zero minutes'                              => ['PT0M',                    'PT0S'],
            'Zero seconds'                              => ['PT0S',                    'PT0S'],
            'Zero YMD'                                  => ['P0Y0M0D',                 'PT0S'],
            'Zero HMS'                                  => ['PT0H0M0S',                'PT0S'],
            'Zero YMD HMS'                              => ['P0Y0M0DT0H0M0S',          'PT0S'],
            'Zero months and minutes'                   => ['P0MT0M',                  'PT0S'],

            // Duration Format - Weeks
            'Weeks 1'                                   => ['P1W',                     'P7D'],
            'Weeks 3'                                   => ['P3W',                     'P21D'],
            'Weeks 10'                                  => ['P10W',                    'P70D'],
            'Weeks zero prefix'                         => ['P03W',                    'P21D'],

            // Duration Format - Single digit
            'Years single digit'                         => ['P2Y',                    'P730DT12H'],
            'Months single digit'                        => ['P2M',                    'P60DT21H'],
            'Days single digit'                          => ['P2D',                    'P2D'],
            'Hours single digit'                         => ['PT2H',                   'PT2H'],
            'Minutes single digit'                       => ['PT2M',                   'PT2M'],
            'Seconds single digit'                       => ['PT2S',                   'PT2S'],

            // Duration Format - Double digit
            'Years double digit'                         => ['P12Y',                   'P4383D'],
            'Months double digit'                        => ['P12M',                   'P365DT6H'],
            'Days double digit'                          => ['P12D',                   'P12D'],
            'Hours double digit'                         => ['PT12H',                  'PT12H'],
            'Minutes double digit'                       => ['PT12M',                  'PT12M'],
            'Seconds double digit'                       => ['PT12S',                  'PT12S'],

            // Duration Format - Single digit with zero prefix
            'Years single digit zero prefix'             => ['P05Y',                   'P1826DT6H'],
            'Months single digit zero prefix'            => ['P05M',                   'P152DT4H30M'],
            'Days single digit zero prefix'              => ['P05D',                   'P5D'],
            'Hours single digit zero prefix'             => ['PT05H',                  'PT5H'],
            'Minutes single digit zero prefix'           => ['PT05M',                  'PT5M'],
            'Seconds single digit zero prefix'           => ['PT05S',                  'PT5S'],

            // Duration Format - Double digit with zero prefix
            'Years double digit zero prefix'             => ['P012Y',                  'P4383D'],
            'Months double digit zero prefix'            => ['P012M',                  'P365DT6H'],
            'Days double digit zero prefix'              => ['P012D',                  'P12D'],
            'Hours double digit zero prefix'             => ['PT012H',                 'PT12H'],
            'Minutes double digit zero prefix'           => ['PT012M',                 'PT12M'],
            'Seconds double digit zero prefix'           => ['PT012S',                 'PT12S'],

            // Duration Format - Multiple periods
            'Years and months'                          => ['P2Y3M',                   'P821DT19H30M'],
            'Months and days'                           => ['P2M3D',                   'P63DT21H'],
            'Days and hours'                            => ['P2DT3H',                  'P2DT3H'],
            'Hours and minutes'                         => ['PT2H3M',                  'PT2H3M'],
            'Minutes and seconds'                       => ['PT2M3S',                  'PT2M3S'],
            'Seconds and years'                         => ['P3YT2S',                  'P1095DT18H2S'],

            // Duration Format - Full periods
            'Full YMD'                                  => ['P1Y2M3D',                 'P429DT3H'],
            'Full HMS'                                  => ['PT4H5M6S',                'PT4H5M6S'],
            'Full YMD HMS'                              => ['P1Y2M3DT4H5M6S',          'P429DT7H5M6S'],

            // Date Time Format - Misc
            'Date time basic all zero'                  => ['P00000000T000000',        'PT0S'],
            'Date time extended all zero'               => ['P0000-00-00T00:00:00',    'PT0S'],
            'Date time basic'                           => ['P00010203T040506',        'P429DT7H5M6S'],
            'Date time extended'                        => ['P0001-02-03T04:05:06',    'P429DT7H5M6S'],
        ];
    }

    /**
     * @dataProvider invalidIsoStrings
     */
    public function testFromIsoStringWithInvalidIsoString($isoString, $expected)
    {
        $this->expectException('InvalidArgumentException', $expected);
        Duration::fromIsoString($isoString);
    }

    public function invalidIsoStrings()
    {
        return [
            // Duration Format - Empty/Zero
            'Missing P'                                 => ['',                        'Invalid ISO duration: "".'],
            'Missing P has digit'                       => ['2',                       'Invalid ISO duration: "2".'],
            'Missing P has digit designator'            => ['D',                       'Invalid ISO duration: "D".'],
            'Missing P has digit and designator'        => ['2D',                      'Invalid ISO duration: "2D".'],
            'Missing P has digit and designator dupe'   => ['2D2D',                    'Invalid ISO duration: "2D2D".'],
            'Missing P has spaces'                      => [' ',                       'Invalid ISO duration: " ".'],
            'Empty P'                                   => ['P',                       'Invalid ISO duration: "P".'],
            'Empty P with ending T'                     => ['PT',                      'Invalid ISO duration: "PT".'],
            'P with space prefix'                       => [' P',                      'Invalid ISO duration: " P".'],
            'P with space postfix'                      => ['P ',                      'Invalid ISO duration: "P ".'],
            'P with space pre/post fix'                 => [' P ',                     'Invalid ISO duration: " P ".'],

            // Duration Format - Misc
            'Missing period designator'                 => ['P2',                      'Invalid ISO duration: "P2".'],
            'Duplicate period designator'               => ['P2Y2Y',                   'Invalid ISO duration: "P2Y2Y".'],
            'Missing T before hours designator'         => ['P2H',                     'Invalid ISO duration: "P2H".'],
            'Missing T before seconds designator'       => ['P2S',                     'Invalid ISO duration: "P2S".'],
            'Invalid negative period'                   => ['P-2Y',                    'Invalid ISO duration: "P-2Y".'],
            'Years after T time marker'                 => ['PT2Y',                    'Invalid ISO duration: "PT2Y".'],
            'Days after T time marker'                  => ['PT2D',                    'Invalid ISO duration: "PT2D".'],
            'Years and days after T time marker'        => ['PT1Y2M3D',                'Invalid ISO duration: "PT1Y2M3D".'],
            'Zero period ends with T'                   => ['P0YT',                    'Invalid ISO duration: "P0YT".'],
            'Double digit zero ends with T'             => ['P00YT',                   'Invalid ISO duration: "P00YT".'],
            'Double digit ends with T'                  => ['P10YT',                   'Invalid ISO duration: "P10YT".'],
            'Year ends with T'                          => ['P1YT',                    'Invalid ISO duration: "P1YT".'],
            'Month ends with T'                         => ['P1MT',                    'Invalid ISO duration: "P1MT".'],
            'Day ends with T'                           => ['P1DT',                    'Invalid ISO duration: "P1DT".'],
            'Multiple periods ends with T'              => ['P1Y1MT',                  'Invalid ISO duration: "P1Y1MT".'],
            'Multiple periods ends with T'              => ['P1M1DT',                  'Invalid ISO duration: "P1M1DT".'],
            'Multiple periods ends with T'              => ['P1Y1DT',                  'Invalid ISO duration: "P1Y1DT".'],

            // Date Time Format - Basic
            'Date time basic missing P'                 => ['00010203T040506',         'Invalid ISO duration: "00010203T040506".'],
            'Date time basic missing T'                 => ['P00010203 040506',        'Invalid ISO duration: "P00010203 040506".'],
            'Date time basic missing P and T'           => ['00010203 040506',         'Invalid ISO duration: "00010203 040506".'],
            'Date time basic space prefix'              => [' P00010203T040506',       'Invalid ISO duration: " P00010203T040506".'],
            'Date time basic space postfix'             => ['P00010203T040506 ',       'Invalid ISO duration: "P00010203T040506 ".'],
            'Date time basic space pre/post fix'        => [' P00010203T040506 ',      'Invalid ISO duration: " P00010203T040506 ".'],
            'Date time basic months exceeds moduli'     => ['P00001300T000000',        'Invalid ISO duration: "P00001300T000000".'],
            'Date time basic days exceeds moduli'       => ['P00000032T000000',        'Invalid ISO duration: "P00000032T000000".'],
            'Date time basic hours exceeds moduli'      => ['P00000000T250000',        'Invalid ISO duration: "P00000000T250000".'],
            'Date time basic minutes exceeds moduli'    => ['P00000000T006000',        'Invalid ISO duration: "P00000000T006000".'],
            'Date time basic seconds exceeds moduli'    => ['P00000000T000060',        'Invalid ISO duration: "P00000000T000060".'],

            // Date Time Format - Extended
            'Date time extended missing P'              => ['0001-02-03T04:05:06',     'Invalid ISO duration: "0001-02-03T04:05:06".'],
            'Date time extended missing T'              => ['P0001-02-03 04:05:06',    'Invalid ISO duration: "P0001-02-03 04:05:06".'],
            'Date time extended missing P and T'        => ['0001-02-03 04:05:06',     'Invalid ISO duration: "0001-02-03 04:05:06".'],
            'Date time extended space prefix'           => [' P0001-02-03T04:05:06',   'Invalid ISO duration: " P0001-02-03T04:05:06".'],
            'Date time extended space postfix'          => ['P0001-02-03T04:05:06 ',   'Invalid ISO duration: "P0001-02-03T04:05:06 ".'],
            'Date time extended space pre/post fix'     => [' P0001-02-03T04:05:06 ',  'Invalid ISO duration: " P0001-02-03T04:05:06 ".'],
            'Date time extended months exceeds moduli'  => ['P0000-13-00T00:00:00',    'Invalid ISO duration: "P0000-13-00T00:00:00".'],
            'Date time extended days exceeds moduli'    => ['P0000-00-32T00:00:00',    'Invalid ISO duration: "P0000-00-32T00:00:00".'],
            'Date time extended hours exceeds moduli'   => ['P0000-00-00T25:00:00',    'Invalid ISO duration: "P0000-00-00T25:00:00".'],
            'Date time extended minutes exceeds moduli' => ['P0000-00-00T00:60:00',    'Invalid ISO duration: "P0000-00-00T00:60:00".'],
            'Date time extended seconds exceeds moduli' => ['P0000-00-00T00:00:60',    'Invalid ISO duration: "P0000-00-00T00:00:60".'],
        ];
    }

    public function testFromNativeDateInterval()
    {
        $native = new DateInterval('P3DT4H5M6S');
        $result = Duration::fromNativeDateInterval($native);

        $this->assertSame(3, $result->days());
        $this->assertSame(4, $result->hours());
        $this->assertSame(5, $result->minutes());
        $this->assertSame(6, $result->seconds());
    }

    public function testFromNativeDateIntervalWithInvert()
    {
        $native         = new DateInterval('P3DT4H5M6S');
        $native->invert = 1;

        $result = Duration::fromNativeDateInterval($native);

        $this->assertSame(-3, $result->days());
        $this->assertSame(-4, $result->hours());
        $this->assertSame(-5, $result->minutes());
        $this->assertSame(-6, $result->seconds());
    }

    public function testFromNativeDateIntervalWithInvalidArgumentException()
    {
        $this->expectException('InvalidArgumentException', 'Duration\'s can not be created from date intervals containing years or months.');

        $native = new DateInterval('P1Y2M3DT4H5M6S');
        Duration::fromNativeDateInterval($native);
    }
}
