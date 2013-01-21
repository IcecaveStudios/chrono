<?php
namespace Icecave\Chrono\Interval;

use Icecave\Chrono\Date;
use Icecave\Chrono\IsoRepresentationInterface;
use Icecave\Chrono\Support\Calendar;
use Icecave\Chrono\TypeCheck\TypeCheck;

class Month extends AbstractInterval implements IsoRepresentationInterface
{
    /**
     * @param Year    $year     The year.
     * @param integer $ordinal  The month number.
     */
    public function __construct(Year $year, $ordinal)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->year = $year;
        $this->ordinal = $ordinal;
    }

    /**
     * @return Year
     */
    public function year()
    {
        $this->typeCheck->year(func_get_args());

        return $this->year;
    }

    /**
     * @return integer The year number.
     */
    public function ordinal()
    {
        $this->typeCheck->ordinal(func_get_args());

        return $this->ordinal;
    }

    /**
     * @return Date The start of the interval.
     */
    public function start()
    {
        $this->typeCheck->start(func_get_args());

        return new Date($this->year()->ordinal(), $this->ordinal(), 1);
    }

    /**
     * @return Date The end of the interval.
     */
    public function end()
    {
        $this->typeCheck->end(func_get_args());

        return new Date($this->year()->ordinal(), $this->ordinal(), $this->numberOfDays());
    }

    /**
     * @return integer The number of days included in this interval.
     */
    public function numberOfDays()
    {
        $this->typeCheck->numberOfDays(func_get_args());

        Calendar::daysInMonth($this->year()->ordinal(), $this->ordinal());
    }

    /**
     * @link http://en.wikipedia.org/wiki/ISO_8601
     *
     * @return string A string representing this object in an ISO compatible format (YYYY-MM).
     */
    public function isoString()
    {
        $this->typeCheck->isoString(func_get_args());

        return sprintf(
            '%s-%02d',
            $this->year(),
            $this->ordinal()
        );
    }

    /**
     * @link http://en.wikipedia.org/wiki/ISO_8601
     *
     * @return string A string representing this object in an ISO compatible format (YYYY-MM).
     */
    public function __toString()
    {
        $this->typeCheck->validateString(func_get_args());

        return $this->isoString();
    }

    private $typeCheck;
    private $ordinal;
}
