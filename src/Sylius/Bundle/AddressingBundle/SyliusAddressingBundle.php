<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Sylius addressing and zones management bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusAddressingBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Addressing\Model\AddressInterface'    => 'sylius.model.address.class',
            'Sylius\Component\Addressing\Model\CountryInterface'    => 'sylius.model.country.class',
            'Sylius\Component\Addressing\Model\ProvinceInterface'   => 'sylius.model.province.class',
            'Sylius\Component\Addressing\Model\ZoneInterface'       => 'sylius.model.zone.class',
            'Sylius\Component\Addressing\Model\ZoneMemberInterface' => 'sylius.model.zone_member.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Addressing\Model';
    }
}
