<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sales order management bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusOrderBundle extends Bundle
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
            'Sylius\Component\Order\Model\OrderInterface'      => 'sylius.model.order.class',
            'Sylius\Component\Order\Model\OrderItemInterface'  => 'sylius.model.order_item.class',
            'Sylius\Component\Order\Model\AdjustmentInterface' => 'sylius.model.adjustment.class',
            'Sylius\Component\Order\Model\NumberInterface'     => 'sylius.model.number.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_order', $interfaces));

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Sylius\Component\Order\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_order.driver.doctrine/orm'));
    }
}
