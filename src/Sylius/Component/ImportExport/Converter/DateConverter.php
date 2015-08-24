<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Converter;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class DateConverter implements DateConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function toString(\DateTime $date, $format)
    {
        return $date->format($format);
    }

    /**
     * {@inheritdoc}
     */
    public function toDateTime($stringDate, $format)
    {
        if (false === ($date = \DateTime::createFromFormat($format, $stringDate))) {
            throw new \InvalidArgumentException('Given format is invalid.');
        }

        return $date;
    }
}
