<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;

/**
 * Sylius locale Twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleHelper
     */
    protected $helper;

    /**
     * Meta data for special languages,
     * by default we assume that all languages are LTR except mentioned otherwise.
     *
     * @var array
     */
    protected static $localeMetaData = array(
        // TODO Complete the list if keeping the meta-data like this is a good practice at all!
        'fa' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'fa_IR' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'fa_AF' => array(
            'rtl' => true,
            'calendar' => 'persian'
        ),
        'ar' => array(
            'rtl' => true,
            'calendar' => 'islamic'
        )
    );

    /**
     * @param LocaleHelper $helper
     */
    public function __construct(LocaleHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFunction('sylius_locale', array($this, 'getLocale')),
            new \Twig_SimpleFunction('sylius_language', array($this, 'getLanguage')),
            new \Twig_SimpleFunction('sylius_rtl', array($this, 'isRtl')),

            // Override twig's default date() filter for calendar-aware date conversion
            new \Twig_SimpleFilter('date', array($this, 'dateFilter'),
                array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_language', array($this, 'getLanguage')),
            new \Twig_SimpleFunction('sylius_rtl', array($this, 'isRtl')),
            new \Twig_SimpleFunction('sylius_calendar', array($this, 'getCalendar')),
        );
    }

    /**
     * Get currently selected locale code.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->helper->getLocale();
    }

    /**
     * Returns localized language name from context or passed locale code
     *
     * @param string|null $code
     * @return string
     */
    public function getLanguage($code = null)
    {
        if ($code === null) {
            $code = $this->getLocale();
        }

        return Intl::getLocaleBundle()->getLocaleName($code);
    }

    /**
     * @inheritdoc
     */
    public function getCalendar()
    {
        $code = $this->getLocale();
        return @self::$localeMetaData[$code]['calendar'] ?: 'gregorian';
    }

    /**
     * @inheritdoc
     */
    public function isRtl($code = null) {

        if ($code == null) {
            $code = $this->getLocale();
        }

        return @self::$localeMetaData[$code]['rtl'] ?: false;
    }

    /**
     * Twig filter to format date in a calendar-aware fashion
     *
     * @param \Twig_Environment $env
     * @param $date
     * @param $timezone
     * @param $format
     * @return bool|string
     * @throws \Twig_Error_Runtime
     */
    public function dateFilter(\Twig_Environment $env, $date, $format = null, $timezone = null)
    {
        if (null === $format) {
            $formats = $env->getExtension('core')->getDateFormat();
            $format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
        }

        // Determine the timezone
        if (!$timezone) {
            $defaultTimezone = $env->getExtension('core')->getTimezone();
        } elseif (!$timezone instanceof \DateTimeZone) {
            $defaultTimezone = new \DateTimeZone($timezone);
        } else {
            $defaultTimezone = $timezone;
        }

        $locale = $this->getLocale();
        $calendar = $this->getCalendar();

        if ($date instanceof \DateTime || $date instanceof \DateTimeInterface) {
            $date = clone $date;

            if (false !== $timezone) {
                $date->setTimezone($defaultTimezone);
            }

        } else {

            $asString = (string) $date;
            if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
                $date = '@' . $date;
            }

            $date = new \DateTime($date, @$timezone ?: $defaultTimezone);
        }

        if ('gregorian' === $calendar || null === $calendar) {
            return $date->format($format);
        } else {
            $dateFormatter = new \IntlDateFormatter("$locale@calendar=$calendar", null, null, @$timezone ?: $defaultTimezone, \IntlDateFormatter::TRADITIONAL, self::convertDatePhpToIcu($format));
            return $dateFormatter->format($date);
        }
    }

    /**
     * The method below is grabbed from Yii Project
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