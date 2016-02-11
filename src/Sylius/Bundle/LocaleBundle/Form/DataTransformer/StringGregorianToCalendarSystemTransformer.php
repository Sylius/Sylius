<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Form\DataTransformer;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;
use Sylius\Component\Locale\Calendars;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class StringGregorianToCalendarSystemTransformer implements DataTransformerInterface
{
    /**
     * @var string PHP-compatible date format
     */
    private $format;

    /**
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     * @param string       $format       PHP-compatible date format
     * @param LocaleHelper $localeHelper
     */
    public function __construct($format, LocaleHelper $localeHelper)
    {
        $this->format = $format;
        $this->localeHelper = $localeHelper;
    }

    /**
     * @param string $date Normalized date.
     *
     * @return string Localized calendar-aware date.
     */
    public function transform($date)
    {
        if (Calendars::GREGORIAN === $this->localeHelper->getCurrentCalendar()) {
            return $date;
        }

        // Since Symfony's default DateType is using \IntlDateFormatter
        // but not correctly (Only sets locale and not calendar system)
        // we need to use a IntlDateFormatter similar to one DateType used
        // to change back everything to the original \DateTime that was used.
        //
        // For example when you set locale to `persian` it converts 2014
        // to it's persian character (۲۰۱۴) representation.
        // So we need to change it back to latin characters.
        $originalFormatter = new \IntlDateFormatter(
            \Locale::getDefault(),
            null,
            null,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
            LocaleHelper::convertDatePhpToIcu($this->format)
        );

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($originalFormatter->parse($date));

        $translatedDate = $this->localeHelper->formatDate($dateTime, $this->format);

        return $translatedDate;
    }

    /**
     * @param string $value Localized calendar-aware date.
     *
     * @return string Normalized date.
     */
    public function reverseTransform($value)
    {
        if (Calendars::GREGORIAN === $this->localeHelper->getCurrentCalendar()) {
            return $value;
        }

        $dateTime = $this->localeHelper->parseDate($value, $this->format);

        return $dateTime->format($this->format);
    }
}
