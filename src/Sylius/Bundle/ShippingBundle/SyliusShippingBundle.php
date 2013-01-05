<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Shipping component for Symfony2 e-commerce applications.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusShippingBundle extends Bundle
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
            'Sylius\Bundle\ShippingBundle\Model\ShipmentInterface'         => 'sylius_shipping.model.shipment.class',
            'Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface'     => 'sylius_shipping.model.shipment_item.class',
            'Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface' => 'sylius_shipping.model.category.class',
            'Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface'   => 'sylius_shipping.model.method.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_shipping', $interfaces));
        $container->addCompilerPass(new RegisterCalculatorsPass());
    }
}
