<?php
namespace Icecave\Chrono;

use Icecave\Chrono\TypeCheck\TypeCheck;

/**
 * Represents a date.
 */
class Date implements TimePointInterface
{
    /**
     * @param integer $year  The year component of the date.
     * @param integer $month The month component of the date.
     * @param integer $day   The day component of the date.
     */
    public function __construct($year, $month, $day)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    /**
     * @return integer The year component of the date.
     */
    public function year()
    {
        $this->typeCheck->year(func_get_args());

        return $this->year;
    }

    /**
     * @return integer The month component of the date.
     */
    public function month()
    {
        $this->typeCheck->month(func_get_args());

        return $this->month;
    }

    /**
     * @return integer The day component of the date.
     */
    public function day()
    {
        $this->typeCheck->day(func_get_args());

        return $this->day;
    }

    /**
     * Perform a {@see strcmp} style comparison with another time point.
     *
     * @param TimePointInterface $timePoint The time point to compare.
     *
     * @return integer 0 if $this and $timePoint are equal, <0 if $this < $timePoint, or >0 if $this > $timePoint.
     */
    public function compare(TimePointInterface $timePoint)
    {
        $this->typeCheck->compare(func_get_args());

        return strcmp($this->isoString(), $timePoint->isoString());
    }

    /**
     * @link http://en.wikipedia.org/wiki/ISO_8601
     *
     * @return string A string representing this object in an ISO compatible format (YYYY-MM-DD).
     */
    public function isoString()
    {
        $this->typeCheck->isoString(func_get_args());

        return sprintf(
            '%04d-%02d-%02d',
            $this->year(),
            $this->month(),
            $this->day()
        );
    }

    /**
     * @link http://en.wikipedia.org/wiki/ISO_8601
     *
     * @return string A string representing this object in an ISO compatible format (YYYY-MM-DD).
     */
    public function __toString()
    {
        $this->typeCheck->validateString(func_get_args());

        return $this->isoString();
    }

    private $typeCheck;
    private $year;
    private $month;
    private $day;
}
