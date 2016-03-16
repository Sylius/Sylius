<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Converter;

use Symfony\Component\Intl\Intl;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CountryNameConverter implements CountryNameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToCode($name, $locale = 'en')
    {
        $names = Intl::getRegionBundle()->getCountryNames($locale);
        $countryCode = array_search($name, $names, true);

        if (false === $countryCode) {
            throw new \InvalidArgumentException(sprintf(
                'Country "%s" not found! Available names: %s.', $name, implode(', ', $names)
            ));
        }

        return $countryCode;
    }
}
