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
     * @param string $provinceCode
     *
     * @return string
     */
    public function getProvinceName($provinceCode)
    {
        return $this->provinceNamingProvider->getName($provinceCode);
    }

    /**
     * @param string $provinceCode
     *
     * @return string
     */
    public function getProvinceAbbreviation($provinceCode)
    {
        return $this->provinceNamingProvider->getAbbreviation($provinceCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_naming';
    }
}
