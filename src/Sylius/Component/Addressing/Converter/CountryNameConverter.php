<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Converter;

use Symfony\Component\Intl\Intl;

final class CountryNameConverter implements CountryNameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToCode(string $name, string $locale = 'en'): string
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
