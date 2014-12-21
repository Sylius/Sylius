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
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class ArrayGregorianToCalendarSystemTransformer implements DataTransformerInterface {

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
    public function __construct($localeHelper)
    {
        $this->localeHelper = $localeHelper;
    }

    /**
     * Transforms a date array into a calendar-aware localized date array.
     *
     * @param array $date Normalized date array.
     * @return array Translated localized date array.
     */
    public function transform($date)
    {
        if (!is_array($date)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ($this->localeHelper->getCalendar() !== 'gregorian') {

            $dateTime = \DateTime::createFromFormat('Y-m-d', $date['year'] . '-' . $date['month'] . '-' . $date['day']);
            $translatedDate = $this->localeHelper->formatDate($dateTime, 'Y-m-d');

            list($year, $month, $day) = explode('-', $translatedDate);

            return array(
                'year' => $year,
                'month' => $month,
                'day' => $day
            );

        } else {
            return $date;
        }
    }

    /**
     * Transforms a calendar-aware localized date into a normalized date.
     *
     * @param array $value Normalized date array
     *
     * @return array Normalized date
     *
     * @throws TransformationFailedException If the given value is not an array,
     *                                       if the value could not be transformed
     *                                       or if the input timezone is not
     *                                       supported.
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ($this->localeHelper->getCalendar() !== 'gregorian') {

            $dateTime = $this->localeHelper->parseDate($value['year'] . '-' . $value['month'] . '-' . $value['day'], 'Y-m-d');
            list($year, $month, $day) = explode('-', $dateTime->format('Y-m-d'));

            return array(
                'year' => $year,
                'month' => $month,
                'day' => $day
            );

        } else {
            return $value;
        }
    }
} 