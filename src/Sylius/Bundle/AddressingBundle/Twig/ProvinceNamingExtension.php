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

use Sylius\Component\Addressing\Provider\ProvinceNamingProvider;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNamingExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_province_name', [ProvinceNamingProvider::class, 'getProvinceName']),
            new \Twig_SimpleFilter('sylius_province_abbreviation', [ProvinceNamingProvider::class, 'getProvinceAbbreviation']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_naming';
    }
}
