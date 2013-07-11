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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sylius addressing and zones management bundle.
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
            'Sylius\Bundle\AddressingBundle\Model\AddressInterface'    => 'sylius.model.address.class',
            'Sylius\Bundle\AddressingBundle\Model\CountryInterface'    => 'sylius.model.country.class',
            'Sylius\Bundle\AddressingBundle\Model\ProvinceInterface'   => 'sylius.model.province.class',
            'Sylius\Bundle\AddressingBundle\Model\ZoneInterface'       => 'sylius.model.zone.class',
            'Sylius\Bundle\AddressingBundle\Model\ZoneMemberInterface' => 'sylius.model.zone_member.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_addressing', $interfaces));

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Sylius\Bundle\AddressingBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_addressing.driver.doctrine/orm'));
    }
}
