<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class LocaleHelper extends Helper
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * Constructor.
     *
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * Get currently used locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->localeContext->getLocale();
    }

    /**
     * Get currently active calendar system.
     *
     * @return string
     */
    public function getCalendar()
    {
        return $this->localeContext->getCalendar();
    }

    /**
     * Get currently active language direction.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->localeContext->getDirection();
    }

    /**
     * Format date in a calendar-aware fashion
     *
     * @param string|\DateTime     $date     Date to format
     * @param string               $format   PHP-compatible date format
     * @param string|\DateTimeZone $timezone Timezone
     * @param string               $calendar Calendar name
     *
     * @return bool|string Calendar-aware localized formatted date
     */
    public function formatDate($date, $format = null, $timezone = null, $locale = null, $calendar = null)
    {
        // Determine the timezone
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }

        if (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }

        if ($locale === null) {
            $locale = $this->localeContext->getLocale();
        }

        if ($calendar === null) {
            $calendar = $this->localeContext->getCalendar();
        }

        if ($date instanceof \DateTime || $date instanceof \DateTimeInterface) {
            $date = clone $date;

            if (false !== $timezone) {
                $date->setTimezone($timezone);
            }
        } else {
            // If $date's string representation is somehow numeric,
            // we'll assume it's a timestamp then prepend an @ and pass it to \DateTime.
            $asString = (string) $date;
            if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
                $date = '@' . $date;
            }

            $date = new \DateTime($date, $timezone);
        }

        if ('gregorian' === $calendar || null === $calendar) {
            return $date->format($format);
        } else {
            $dateFormatter = new \IntlDateFormatter("$locale@calendar=$calendar", null, null, $timezone, \IntlDateFormatter::TRADITIONAL, self::convertDatePhpToIcu($format));
            return $dateFormatter->format($date);
        }
    }

    /**
     * Parses a calendar-aware localized date string to \DateTime
     *
     * @param string               $date     Date to parse
     * @param string               $format   PHP-compatible date format
     * @param string|\DateTimeZone $timezone Timezone
     * @param string               $calendar Calendar name
     *
     * @return \DateTime
     */
    public function parseDate($date, $format, $timezone = null, $locale = null, $calendar = null)
    {
        // Determine the timezone
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }

        if (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }

        if ($locale === null) {
            $locale = $this->localeContext->getLocale();
        }

        if ($calendar === null) {
            $calendar = $this->localeContext->getCalendar();
        }

        $dateTime = new \DateTime(null, $timezone);

        if ('gregorian' === $calendar || null === $calendar) {
            $dateTime->setTimestamp(strtotime($date));
        } else {
            $dateFormatter = new \IntlDateFormatter("$locale@calendar=$calendar", null, null, $timezone, \IntlDateFormatter::TRADITIONAL, self::convertDatePhpToIcu($format));
            $dateTime->setTimestamp($dateFormatter->parse($date));
        }

        return $dateTime;
    }

    /**
     * The method below is borrowed from Yii Project
     * (https://github.com/yiisoft/yii2/blob/master/framework/helpers/BaseFormatConverter.php#L241)
     *
     * Converts a date format pattern from [php date() function format][] to [ICU format][].
     *
     * The conversion is limited to date patterns that do not use escaped characters.
     * Patterns like `jS \o\f F Y` which will result in a date like `1st of December 2014` may not be converted correctly
     * because of the use of escaped characters.
     *
     * Pattern constructs that are not supported by the ICU format will be removed.
     *
     * [php date() function format]: http://php.net/manual/en/function.date.php
     * [ICU format]: http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
     *
     * @param string $pattern date format pattern in php date()-function format.
     *
     * @return string The converted date format pattern.
     */
    public static function convertDatePhpToIcu($pattern)
    {
        // Translation from PHP DateTime to UCI pattern
        // http://php.net/manual/en/function.date.php
        return strtr($pattern, array(
            // Day
            'd' => 'dd',    // Day of the month, 2 digits with leading zeros 	01 to 31
            'D' => 'eee',   // A textual representation of a day, three letters 	Mon through Sun
            'j' => 'd',     // Day of the month without leading zeros 	1 to 31
            'l' => 'eeee',  // A full textual representation of the day of the week 	Sunday through Saturday
            'N' => 'e',     // ISO-8601 numeric representation of the day of the week, 1 (for Monday) through 7 (for Sunday)
            'S' => '',      // English ordinal suffix for the day of the month, 2 characters 	st, nd, rd or th. Works well with j
            'w' => '',      // Numeric representation of the day of the week 	0 (for Sunday) through 6 (for Saturday)
            'z' => 'D',     // The day of the year (starting from 0) 	0 through 365
            // Week
            'W' => 'w',     // ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0) 	Example: 42 (the 42nd week in the year)
            // Month
            'F' => 'MMMM',  // A full textual representation of a month, January through December
            'm' => 'MM',    // Numeric representation of a month, with leading zeros 	01 through 12
            'M' => 'MMM',   // A short textual representation of a month, three letters 	Jan through Dec
            'n' => 'M',     // Numeric representation of a month, without leading zeros 	1 through 12, not supported by ICU but we fallback to "with leading zero"
            't' => '',      // Number of days in the given month 	28 through 31
            // Year
            'L' => '',      // Whether it's a leap year, 1 if it is a leap year, 0 otherwise.
            'o' => 'Y',     // ISO-8601 year number. This has the same value as Y, except that if the ISO week number (W) belongs to the previous or next year, that year is used instead.
            'Y' => 'yyyy',  // A full numeric representation of a year, 4 digits 	Examples: 1999 or 2003
            'y' => 'yy',    // A two digit representation of a year 	Examples: 99 or 03
            // Time
            'a' => 'a',     // Lowercase Ante meridiem and Post meridiem, am or pm
            'A' => 'a',     // Uppercase Ante meridiem and Post meridiem, AM or PM, not supported by ICU but we fallback to lowercase
            'B' => '',      // Swatch Internet time 	000 through 999
            'g' => 'h',     // 12-hour format of an hour without leading zeros 	1 through 12
            'G' => 'H',     // 24-hour format of an hour without leading zeros 0 to 23h
            'h' => 'hh',    // 12-hour format of an hour with leading zeros, 01 to 12 h
            'H' => 'HH',    // 24-hour format of an hour with leading zeros, 00 to 23 h
            'i' => 'mm',    // Minutes with leading zeros 	00 to 59
            's' => 'ss',    // Seconds, with leading zeros 	00 through 59
            'u' => '',      // Microseconds. Example: 654321
            // Timezone
            'e' => 'VV',    // Timezone identifier. Examples: UTC, GMT, Atlantic/Azores
            'I' => '',      // Whether or not the date is in daylight saving time, 1 if Daylight Saving Time, 0 otherwise.
            'O' => 'xx',    // Difference to Greenwich time (GMT) in hours, Example: +0200
            'P' => 'xxx',   // Difference to Greenwich time (GMT) with colon between hours and minutes, Example: +02:00
            'T' => 'zzz',   // Timezone abbreviation, Examples: EST, MDT ...
            'Z' => '',    // Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. -43200 through 50400
            // Full Date/Time
            'c' => 'yyyy-MM-dd\'T\'HH:mm:ssxxx', // ISO 8601 date, e.g. 2004-02-12T15:19:21+00:00
            'r' => 'eee, dd MMM yyyy HH:mm:ss xx', // RFC 2822 formatted date, Example: Thu, 21 Dec 2000 16:01:07 +0200
            'U' => '',      // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
