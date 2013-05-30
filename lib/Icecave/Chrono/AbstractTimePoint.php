<?php
namespace Icecave\Chrono;

use Icecave\Chrono\TimeSpan\Duration;
use Icecave\Chrono\TimeSpan\Period;
use Icecave\Chrono\TypeCheck\TypeCheck;
use Icecave\Parity\AbstractComparable;
use Icecave\Parity\Exception\NotComparableException;

/**
 * Represents a concrete point on the time continuum.
 */
abstract class AbstractTimePoint extends AbstractComparable implements TimePointInterface
{
    public function __construct()
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());
    }

    /**
     * Check if $this is able to be compared to another value.
     *
     * A return value of false indicates that calling $this->compare($value)
     * will throw an exception.
     *
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this can be compared to $value.
     */
    public function canCompare($value)
    {
        $this->typeCheck->canCompare(func_get_args());

        return $value instanceof TimePointInterface;
    }

    /**
     * Compare this object with another value, yielding a result according to the following table:
     *
     * +--------------------+---------------+
     * | Condition          | Result        |
     * +--------------------+---------------+
     * | $this == $value    | $result === 0 |
     * | $this < $value     | $result < 0   |
     * | $this > $value     | $result > 0   |
     * +--------------------+---------------+
     *
     * @param mixed $timePoint The time point to compare.
     *
     * @return integer                0 if $this and $timePoint are equal, <0 if $this < $timePoint, or >0 if $this > $timePoint.
     * @throws NotComparableException Indicates that the implementation does not know how to compare $this to $timePoint.
     */
    public function compare($timePoint)
    {
        $this->typeCheck->compare(func_get_args());

        if (!$this->canCompare($timePoint)) {
            throw new NotComparableException($this, $timePoint);
        }

        return $this->unixTime() - $timePoint->unixTime();
    }

    /**
     * Calculate the difference between this time point and another in seconds.
     *
     * @param TimePointInterface $timePoint
     *
     * @return integer
     */
    public function differenceAsSeconds(TimePointInterface $timePoint)
    {
        $this->typeCheck->differenceAsSeconds(func_get_args());

        return $this->unixTime() - $timePoint->unixTime();
    }

    /**
     * Calculate the difference between this time point and another, representing the result as a duration.
     *
     * @param TimePointInterface $timePoint
     *
     * @return Duration
     */
    public function differenceAsDuration(TimePointInterface $timePoint)
    {
        $this->typeCheck->differenceAsDuration(func_get_args());

        return new Duration($this->differenceAsSeconds($timePoint));
    }

    /**
     * Calculate the difference between this time point and another, representing the result as a duration.
     *
     * @param TimePointInterface $timePoint
     *
     * @return Period
     */
    public function differenceAsPeriod(TimePointInterface $timePoint)
    {
        $this->typeCheck->differenceAsPeriod(func_get_args());

        $timePoint = $timePoint->toTimeZone($this->timeZone());

        return new Period(
            $this->year() - $timePoint->year(),
            $this->month() - $timePoint->month(),
            $this->day() - $timePoint->day(),
            $this->hours() - $timePoint->hours(),
            $this->minutes() - $timePoint->minutes(),
            $this->seconds() - $timePoint->seconds()
        );
    }

    private $typeCheck;
}
