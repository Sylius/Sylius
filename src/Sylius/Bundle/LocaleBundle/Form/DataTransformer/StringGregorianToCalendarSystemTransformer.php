<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Aram Alipoor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Form\DataTransformer;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class StringGregorianToCalendarSystemTransformer implements DataTransformerInterface {

    /**
     * @var string PHP date format
     */
    private $format;

    /**
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     * Constructor.
     *
     * @param $format
     * @param $localeHelper
     *
     */
    public function __construct($format, $localeHelper)
    {
        $this->format = $format;
        $this->localeHelper = $localeHelper;
    }

    /**
     * Transforms a date string into a calendar-aware localized date string.
     *
     * @param string $date Normalized date.
     * @return string Translated localized date.
     */
    public function transform($date)
    {
        if ($this->localeHelper->getCalendar() !== 'gregorian') {

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
        } else {
            return $date;
        }
    }

    public function reverseTransform($value)
    {
        if ($this->localeHelper->getCalendar() !== 'gregorian') {

            $dateTime = $this->localeHelper->parseDate($value, $this->format);
            return $dateTime->format($this->format);

        } else {
            return $value;
        }
    }
}