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

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Component\Shipping\Model\RuleInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Shipping component for Symfony2 applications.
 * It is used as a base for shipments management system inside Sylius.
 *
 * It is fully decoupled, so you can integrate it into your existing project.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusShippingBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCalculatorsPass());
        $container->addCompilerPass(new RegisterRuleCheckersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            ShipmentInterface::class => 'sylius.model.shipment.class',
            ShipmentUnitInterface::class => 'sylius.model.shipment_unit.class',
            ShippingCategoryInterface::class => 'sylius.model.shipping_category.class',
            ShippingMethodInterface::class => 'sylius.model.shipping_method.class',
            RuleInterface::class => 'sylius.model.shipping_method_rule.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Shipping\Model';
    }
}
