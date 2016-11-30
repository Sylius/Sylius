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

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNamingExtension extends \Twig_Extension
{
    /**
     * @var ProvinceNamingProviderInterface
     */
    private $provinceNamingProvider;

    /**
     * @param ProvinceNamingProviderInterface $provinceNamingProvider
     */
    public function __construct(ProvinceNamingProviderInterface $provinceNamingProvider)
    {
        $this->provinceNamingProvider = $provinceNamingProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('sylius_province_name', [$this, 'getProvinceName']),
            new \Twig_SimpleFilter('sylius_province_abbreviation', [$this, 'getProvinceAbbreviation']),
        ];
    }

    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getProvinceName(AddressInterface $address)
    {
        return $this->provinceNamingProvider->getName($address);
    }

    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getProvinceAbbreviation(AddressInterface $address)
    {
        return $this->provinceNamingProvider->getAbbreviation($address);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_naming';
    }
}
