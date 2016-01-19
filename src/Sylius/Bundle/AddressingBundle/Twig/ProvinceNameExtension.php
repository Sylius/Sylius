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

use Sylius\Component\Addressing\Provider\ProvinceNameProviderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceNameExtension extends \Twig_Extension
{
    /**
     * @var ProvinceNameProviderInterface
     */
    private $provinceNameProvider;

    /**
     * @param ProvinceNameProviderInterface $provinceNameProvider
     */
    public function __construct(ProvinceNameProviderInterface $provinceNameProvider)
    {
        $this->provinceNameProvider = $provinceNameProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sylius_province_name', array($this, 'getProvinceName')),
        );
    }

    /**
     * @param string $provinceCode
     *
     * @return string
     */
    public function getProvinceName($provinceCode)
    {
        return $this->provinceNameProvider->get($provinceCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_name';
    }
}
