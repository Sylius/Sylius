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

use Sylius\Bundle\TranslationBundle\AbstractTranslationBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
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
class SyliusShippingBundle extends AbstractTranslationBundle
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
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'          => array(
                'ROLE_SYLIUS_SHIPPING_ADMIN',
                'ROLE_SYLIUS_SHIPPING_CATEGORY_LIST',
                'ROLE_SYLIUS_SHIPPING_METHOD_LIST',
            ),
            'ROLE_SYLIUS_SHIPPING_ADMIN' => array(
                'ROLE_SYLIUS_SHIPPING_LIST',
                'ROLE_SYLIUS_SHIPPING_SHOW',
                'ROLE_SYLIUS_SHIPPING_CREATE',
                'ROLE_SYLIUS_SHIPPING_UPDATE',
                'ROLE_SYLIUS_SHIPPING_DELETE',
            ),
            'ROLE_SYLIUS_SHIPPING_CATEGORY_LIST' => array(
                'ROLE_SYLIUS_SHIPPING_CATEGORY_LIST',
                'ROLE_SYLIUS_SHIPPING_CATEGORY_SHOW',
                'ROLE_SYLIUS_SHIPPING_CATEGORY_CREATE',
                'ROLE_SYLIUS_SHIPPING_CATEGORY_UPDATE',
                'ROLE_SYLIUS_SHIPPING_CATEGORY_DELETE',
            ),
            'ROLE_SYLIUS_SHIPPING_METHOD_LIST' => array(
                'ROLE_SYLIUS_SHIPPING_METHOD_LIST',
                'ROLE_SYLIUS_SHIPPING_METHOD_SHOW',
                'ROLE_SYLIUS_SHIPPING_METHOD_CREATE',
                'ROLE_SYLIUS_SHIPPING_METHOD_UPDATE',
                'ROLE_SYLIUS_SHIPPING_METHOD_DELETE',
            ),
        );
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
        return array(
            'Sylius\Component\Shipping\Model\ShipmentInterface'         => 'sylius.model.shipment.class',
            'Sylius\Component\Shipping\Model\ShipmentItemInterface'     => 'sylius.model.shipment_item.class',
            'Sylius\Component\Shipping\Model\ShippingCategoryInterface' => 'sylius.model.shipping_category.class',
            'Sylius\Component\Shipping\Model\ShippingMethodInterface'   => 'sylius.model.shipping_method.class',
            'Sylius\Component\Shipping\Model\RuleInterface'             => 'sylius.model.shipping_method_rule.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Shipping\Model';
    }
}
