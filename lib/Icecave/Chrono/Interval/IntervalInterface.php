<?php
namespace Icecave\Chrono\Interval;

use Icecave\Chrono\TimePointInterface;
use Icecave\Chrono\TimeSpan\Duration;

/**
 * An interval represents a stretch of time between two known time points.
 */
interface IntervalInterface
{
    /**
     * @return TimePointInterface The start of the interval.
     */
    public function start();

    /**
     * @return TimePointInterface The end of the interval.
     */
    public function end();

    /**
     * @return boolean True if the interval indicates a zero duration; otherwise, false.
     */
    public function isEmpty();

    /**
     * Perform a {@see strcmp} style comparison with another interval.
     *
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return integer 0 if $this and $interval are equal, <0 if $this < $interval, or >0 if $this > $interval.
     */
    public function compare(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this and $interval are equal.
     */
    public function isEqualTo(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this and $interval are not equal.
     */
    public function isNotEqualTo(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this > $interval.
     */
    public function isGreaterThan(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this < $interval.
     */
    public function isLessThan(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this >= $interval.
     */
    public function isGreaterThanOrEqualTo(IntervalInterface $interval);

    /**
     * @param IntervalInterface $interval The interval to compare.
     *
     * @return boolean True if $this <= $interval.
     */
    public function isLessThanOrEqualTo(IntervalInterface $interval);

    /**
     * Check if a given time point is contained within this interval.
     *
     * @param TimePointInterface $timePoint The time point to check.
     *
     * @return boolean True if this interval contains the given time point; otherwise, false.
     */
    public function contains(TimePointInterface $timePoint);

    /**
     * Check if a given interval is contained within this interval.
     *
     * @param IntervalInterface $interval The interval to check.
     *
     * @return boolean True if this interval entirely contains the given interval; otherwise, false.
     */
    public function encompasses(IntervalInterface $interval);

    /**
     * Check if a given interval is at least partially contained within this interval.
     *
     * @param IntervalInterface $interval The interval to check.
     *
     * @return boolean True if this interval intersects the given interval; otherwise, false.
     */
    public function intersects(IntervalInterface $interval);

    /**
     * @return Duration A duration representing the difference between start and end.
     */
    public function duration();
}
