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
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class ArrayGregorianToCalendarSystemTransformer implements DataTransformerInterface
{
    /**
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     * @param $localeHelper
     */
    public function __construct(LocaleHelper $localeHelper)
    {
        $this->localeHelper = $localeHelper;
    }

    /**
     * @param array $date Normalized date array.
     *
     * @return array Localized calendar-aware date array.
     */
    public function transform($date)
    {
        if (!is_array($date)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if (Calendars::GREGORIAN === $this->localeHelper->getCurrentCalendar()) {
            return $date;
        }

        if (empty($date['year']) || empty($date['month']) || empty($date['day'])) {
            return $date;
        }

        $dateTime = \DateTime::createFromFormat('Y-m-d', $date['year'].'-'.$date['month'].'-'.$date['day']);
        $translatedDate = $this->localeHelper->formatDate($dateTime, 'Y-m-d');

        list($year, $month, $day) = explode('-', $translatedDate);

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }

    /**
     * @param array $value Localized calendar-aware date array
     *
     * @return array Normalized date
     *
     * @throws TransformationFailedException If the given value is not an array.
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if (Calendars::GREGORIAN === $this->localeHelper->getCurrentCalendar()) {
            return $value;
        }

        if (empty($value['year']) || empty($value['month']) || empty($value['day'])) {
            return $value;
        }

        // Use helper method `parseDate()` to translate the whole date
        // stored inside the array to its corresponding localized version.
        $dateTime = $this->localeHelper->parseDate($value['year'].'-'.$value['month'].'-'.$value['day'], 'Y-m-d');
        list($year, $month, $day) = explode('-', $dateTime->format('Y-m-d'));

        return [
            'year' => $year,
            'month' => $month,
            'day' => $day,
        ];
    }
}
