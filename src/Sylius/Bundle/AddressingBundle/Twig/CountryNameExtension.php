<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Twig;

use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CountryNameExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_country_name', [$this, 'translateCountryIsoCode']),
        ];
    }

    /**
     * @param mixed  $country
     * @param string $locale
     *
     * @return string
     */
    public function translateCountryIsoCode($country, $locale = null)
    {
        if ($country instanceof CountryInterface) {
            return Intl::getRegionBundle()->getCountryName($country->getCode(), $locale);
        }

        return Intl::getRegionBundle()->getCountryName($country, $locale);
    }
}
