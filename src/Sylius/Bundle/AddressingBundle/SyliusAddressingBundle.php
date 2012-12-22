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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * This bundle provides simple architecture for addresses management.
 * Future plans include zone management, useful for e-commerce applications,
 * where you need set specific tax/shipping rates for concrete zone.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusAddressingBundle extends Bundle
{
    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\AddressingBundle\Model\AddressInterface'    => 'sylius_addressing.model.address.class',
            'Sylius\Bundle\AddressingBundle\Model\CountryInterface'    => 'sylius_addressing.model.country.class',
            'Sylius\Bundle\AddressingBundle\Model\ProvinceInterface'   => 'sylius_addressing.model.province.class',
            'Sylius\Bundle\AddressingBundle\Model\ZoneInterface'       => 'sylius_addressing.model.zone.class',
            'Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface' => 'sylius_addressing.model.zone_member.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_addressing', $interfaces));
    }
}
