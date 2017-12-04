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

namespace Sylius\Bundle\AddressingBundle\Twig;

use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;

class CountryNameExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sylius_country_name', [$this, 'translateCountryIsoCode']),
        ];
    }

    /**
     * @param mixed  $country
     * @param string|null $locale
     *
     * @return string
     */
    public function translateCountryIsoCode($country, ?string $locale = null): string
    {
        if ($country instanceof CountryInterface) {
            return Intl::getRegionBundle()->getCountryName($country->getCode(), $locale);
        }

        return Intl::getRegionBundle()->getCountryName($country, $locale);
    }
}
