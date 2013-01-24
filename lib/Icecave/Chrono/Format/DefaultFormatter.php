<?php
namespace Icecave\Chrono\Format;

use Icecave\Chrono\Date;
use Icecave\Chrono\Time;
use Icecave\Chrono\DateTime;
use Icecave\Chrono\TimeZone;
use Icecave\Chrono\TypeCheck\TypeCheck;
use Icecave\Chrono\Support\Calendar;
use Icecave\Chrono\Support\Ordinal;

/**
 * A string formatter that accets {@see date()} compatible format specifiers.
 */
class DefaultFormatter implements FormatterInterface
{
    public function __construct()
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());
    }

    /**
     * Escape any special characters in the string so that they are not substituted
     * for date/time components when used as a format specifier.
     *
     * @param string $string The string to escape.
     *
     * @return string The escaped string.
     */
    public function escape($string)
    {
        $this->typeCheck->escape(func_get_args());

        return preg_replace(
            '/(?<!\\\\)[' . preg_quote(self::SPECIAL_CHARS, '/') . ']/',
            '\\\\$0',
            $string
        );
    }

    /**
     * @param Date   $date            The date to format.
     * @param string $formatSpecifier The format of the output string.
     *
     * @return string The formatted date.
     */
    public function formatDate(Date $date, $formatSpecifier)
    {
        $this->typeCheck->formatDate(func_get_args());

        return '';
    }

    /**
     * @param Time   $Time            The time to format.
     * @param string $formatSpecifier The format of the output string.
     *
     * @return string The formatted time.
     */
    public function formatTime(Time $Time, $formatSpecifier)
    {
        $this->typeCheck->formatTime(func_get_args());

        return '';
    }

    /**
     * @param DateTime $dateTime        The date/time to format.
     * @param string   $formatSpecifier The format of the output string.
     *
     * @return string The formatted date/time.
     */
    public function formatDateTime(DateTime $dateTime, $formatSpecifier)
    {
        $this->typeCheck->formatDateTime(func_get_args());

        $self = $this;

        return $this->replace(
            $formatSpecifier,
            function ($character) use ($dateTime, $self) {
                switch ($character) {
                    case 'd':
                        return sprintf('%02d', $dateTime->day());
                    case 'D':
                        return Calendar::dayAbbreviation(Calendar::dayOfWeek($dateTime->year(), $dateTime->month(), $dateTime->day()));
                    case 'j':
                        return $dateTime->day();
                    case 'l':
                        return Calendar::dayName(Calendar::dayOfWeek($dateTime->year(), $dateTime->month(), $dateTime->day()));
                    case 'N':
                        return Calendar::dayOfWeek($dateTime->year(), $dateTime->month(), $dateTime->day());
                    case 'S':
                        return Ordinal::suffix($dateTime->day());
                    case 'w':
                        return Calendar::dayOfWeek($dateTime->year(), $dateTime->month(), $dateTime->day(), false);
                    case 'z':
                        return Calendar::dayOfYear($dateTime->year(), $dateTime->month(), $dateTime->day());

                    case 'W':
                        return Calendar::isoWeekNumber($dateTime->year(), $dateTime->month(), $dateTime->day());

                    case 'F':
                        return Calendar::monthName($dateTime->month());
                    case 'm':
                        return sprintf('%02d', $dateTime->month());
                    case 'M':
                        return Calendar::monthAbbreviation($dateTime->month());
                    case 'n':
                        return $dateTime->month();
                    case 't':
                        return Calendar::daysInMonth($dateTime->year(), $dateTime->month());

                    case 'L':
                        return Calendar::isLeapYear($dateTime->year()) ? '1' : '0';
                    case 'o':
                        return Calendar::isoYearNumber($dateTime->year(), $dateTime->month(), $dateTime->day());
                    case 'Y':
                        return $dateTime->year();
                    case 'y':
                        return substr($dateTime->year(), -2);

                    case 'a':
                        return $dateTime->hours() < 12 ? 'am' : 'pm';
                    case 'A':
                        return $dateTime->hours() < 12 ? 'AM' : 'PM';
                    case 'B':
                        return sprintf('%03d', ($dateTime->time()->toTimeZone(new TimeZone(3600))->totalSeconds() / 86400) * 1000);

                    case 'g':
                        return $dateTime->hours() % 12;
                    case 'G':
                        return $dateTime->hours();
                    case 'h':
                        return sprintf('%02d', $dateTime->hours() % 12);
                    case 'H':
                        return sprintf('%02d', $dateTime->hours());
                    case 'i':
                        return sprintf('%02d', $dateTime->minutes());
                    case 's':
                        return sprintf('%02d', $dateTime->seconds());
                    case 'u':
                        return 0;

                    case 'e':
                        break;
                    case 'I':
                        return $dateTime->timeZone()->isDst() ? '1' : '0';
                    case 'O':
                        return str_replace(':', '', $dateTime->timeZone()->isoString());
                    case 'P':
                        return $dateTime->timeZone()->isoString();
                    case 'T':
                        break;
                    case 'Z':
                        return $dateTime->timeZone()->offset();

                    case 'c':
                        return $dateTime->isoString();
                    case 'r':
                        return $self->formatDateTime($dateTime, 'D, d M Y H:i:s O');
                    case 'U':
                        return $dateTime->unixTime();
                }

                return null;
            }
        );
    }

    /**
     * @param TimeZone $timeZone        The timezone to format.
     * @param string   $formatSpecifier The format of the output string.
     *
     * @return string The formatted timezone.
     */
    public function formatTimeZone(TimeZone $timeZone, $formatSpecifier)
    {
        $this->typeCheck->formatTimeZone(func_get_args());

        return '';
    }

    /**
     * @return DefaultFormatter The global instance of the default formatter.
     */
    public static function instance()
    {
        TypeCheck::get(__CLASS__)->instance(func_get_args());

        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * @param string   $formatSpecifier
     * @param callable $callback
     *
     * @return string
     */
    protected function replace($formatSpecifier, $callback)
    {
        $this->typeCheck->replace(func_get_args());

        $length = strlen($formatSpecifier);
        $result = '';
        $escaped = false;

        for ($index = 0; $index < $length; ++$index) {
            $char = $formatSpecifier[$index];

            if ($escaped) {
                $result .= $char;
                $escaped = false;
            } elseif ('\\' === $char) {
                $escaped = true;
            } elseif (false === strpos(self::SPECIAL_CHARS, $char)) {
                $result .= $char;
            } else {
                $value = $callback($char);
                if (null === $value) {
                    $result .= $char;
                } else {
                    $result .= $value;
                }
            }
        }

        // Last character was escape character ...
        if ($escaped) {
            $result .= '\\';
        }

        return $result;
    }

    const SPECIAL_CHARS = 'dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU';

    private static $instance;
    private $typeCheck;
}
