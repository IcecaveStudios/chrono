<?php

namespace Icecave\Chrono;

use DateTime as NativeDateTime;
use Icecave\Chrono\TimeSpan\Duration;
use Icecave\Chrono\TimeSpan\Period;
use Icecave\Chrono\TimeSpan\TimeSpanInterface;
use Icecave\Parity\ExtendedComparable;
use Icecave\Parity\RestrictedComparable;

/**
 * Represents a concrete point on the time continuum.
 */
interface TimePointInterface extends DateInterface, TimeInterface, ExtendedComparable, RestrictedComparable
{
    /**
     * @return int The number of seconds since unix epoch (1970-01-01 00:00:00+00:00).
     */
    public function unixTime();

    /**
     * @return TimeZone The time zone of the time point.
     */
    public function timeZone();

    /**
     * @return NativeDateTime A native PHP DateTime instance representing this time point.
     */
    public function nativeDateTime();

    /**
     * Add a time span to the time point.
     *
     * @param TimeSpanInterface|int $timeSpan A time span instance, or an integer representing seconds.
     *
     * @return TimePointInterface
     */
    public function add($timeSpan);

    /**
     * Subtract a time span from the time point.
     *
     * @param TimeSpanInterface|int $timeSpan A time span instance, or an integer representing seconds.
     *
     * @return TimePointInterface
     */
    public function subtract($timeSpan);

    /**
     * Calculate the difference between this time point and another in seconds.
     *
     * @param TimePointInterface $timePoint
     *
     * @return int
     */
    public function differenceAsSeconds(self $timePoint);

    /**
     * Calculate the difference between this time point and another, representing the result as a duration.
     *
     * @param TimePointInterface $timePoint
     *
     * @return Duration
     */
    public function differenceAsDuration(self $timePoint);

    /**
     * Calculate the difference between this time point and another, representing the result as a duration.
     *
     * @param TimePointInterface $timePoint
     *
     * @return Period
     */
    public function differenceAsPeriod(self $timePoint);
}
