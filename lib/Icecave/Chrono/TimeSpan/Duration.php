<?php
namespace Icecave\Chrono\TimeSpan;

use Icecave\Chrono\TimePointInterface;
use Icecave\Chrono\TypeCheck\TypeCheck;

/**
 * A duration represents a concrete amount of time.
 */
class Duration implements TimeSpanInterface
{
    /**
     * @param integer $seconds The total number of seconds in the duration.
     */
    public function __construct($seconds = 0)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->seconds = $seconds;
    }

    /**
     * @param integer $weeks   The number of weeks in the duration.
     * @param integer $days    The number of days in the duration.
     * @param integer $hours   The number of hours in the duration.
     * @param integer $minutes The number of minutes in the duration.
     * @param integer $seconds The number of seconds in the duration.
     *
     * @return Duration
     */
    public static function fromComponents($weeks = 0, $days = 0, $hours = 0, $minutes = 0, $seconds = 0)
    {
        TypeCheck::get(__CLASS__)->fromComponents(func_get_args());

        $days += $weeks * 7;
        $hours += $days * 24;
        $minutes += $hours * 60;
        $seconds += $minutes * 60;

        return new self($seconds);
    }

    /**
     * @return integer The number of weeks in the duration.
     */
    public function weeks()
    {
        $this->typeCheck->weeks(func_get_args());

        return intval($this->totalSeconds() / 604800);
    }

    /**
     * @return integer The number of days in the duration, not including those that comprise whole weeks.
     */
    public function days()
    {
        $this->typeCheck->days(func_get_args());

        return intval(($this->totalSeconds() % 604800) / 86400);
    }

    /**
     * @return integer The number of hours in the duration, not including those that comprise whole days.
     */
    public function hours()
    {
        $this->typeCheck->hours(func_get_args());

        return intval(($this->totalSeconds() % 86400) / 3600);
    }

    /**
     * @return integer The number of minutes in the duration, not including those that comprise whole hours.
     */
    public function minutes()
    {
        $this->typeCheck->minutes(func_get_args());

        return intval(($this->totalSeconds() % 3600) / 60);
    }

    /**
     * @return integer The number of seconds in the duration, not including those that comprise whole minutes.
     */
    public function seconds()
    {
        $this->typeCheck->seconds(func_get_args());

        return intval($this->totalSeconds() % 60);
    }

    /**
     * @return integer The total number of whole days in the duration.
     */
    public function totalDays()
    {
        $this->typeCheck->totalDays(func_get_args());

        return intval($this->totalSeconds() / 86400);
    }

    /**
     * @return integer The total number of whole hours in the duration.
     */
    public function totalHours()
    {
        $this->typeCheck->totalHours(func_get_args());

        return intval($this->totalSeconds() / 3600);
    }

    /**
     * @return integer The total number of whole minutes in the duration.
     */
    public function totalMinutes()
    {
        $this->typeCheck->totalMinutes(func_get_args());

        return intval($this->totalSeconds() / 60);
    }

    /**
     * @return integer The total number seconds in the duration.
     */
    public function totalSeconds()
    {
        $this->typeCheck->totalSeconds(func_get_args());

        return $this->seconds;
    }

    /**
     * @return boolean True if the duration is zero seconds in length; otherwise, false.
     */
    public function isEmpty()
    {
        $this->typeCheck->isEmpty(func_get_args());

        return 0 === $this->totalSeconds();
    }

    /**
     * Perform a {@see strcmp} style comparison with another duration.
     *
     * @param Duration $duration The duration to compare.
     *
     * @return integer 0 if $this and $duration are equal, <0 if $this < $duration, or >0 if $this > $duration.
     */
    public function compare(Duration $duration)
    {
        $this->typeCheck->compare(func_get_args());

        return $this->totalSeconds() - $duration->totalSeconds();
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this and $duration are equal.
     */
    public function isEqualTo(Duration $duration)
    {
        $this->typeCheck->isEqualTo(func_get_args());

        return $this->compare($duration) === 0;
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this and $duration are not equal.
     */
    public function isNotEqualTo(Duration $duration)
    {
        $this->typeCheck->isNotEqualTo(func_get_args());

        return $this->compare($duration) !== 0;
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this > $duration.
     */
    public function isGreaterThan(Duration $duration)
    {
        $this->typeCheck->isGreaterThan(func_get_args());

        return $this->compare($duration) > 0;
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this < $duration.
     */
    public function isLessThan(Duration $duration)
    {
        $this->typeCheck->isLessThan(func_get_args());

        return $this->compare($duration) < 0;
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this >= $duration.
     */
    public function isGreaterThanOrEqualTo(Duration $duration)
    {
        $this->typeCheck->isGreaterThanOrEqualTo(func_get_args());

        return $this->compare($duration) >= 0;
    }

    /**
     * @param Duration $duration The duration to compare.
     *
     * @return boolean True if $this <= $duration.
     */
    public function isLessThanOrEqualTo(Duration $duration)
    {
        $this->typeCheck->isLessThanOrEqualTo(func_get_args());

        return $this->compare($duration) <= 0;
    }

    /**
     * Resolve the time span to a total number of seconds, using the given time point as the start of the span.
     *
     * @param TimePointInterface $timePoint The start of the time span.
     *
     * @return integer The total number of seconds.
     */
    public function resolve(TimePointInterface $timePoint)
    {
        $this->typeCheck->resolve(func_get_args());

        return $this->totalSeconds();
    }

    private $typeCheck;
    private $seconds;
}