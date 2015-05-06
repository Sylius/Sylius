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

class DateConverter implements DateConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function toString(\DateTime $date, $format)
    {
        return $date->format($format);
    }
}
