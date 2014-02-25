<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Flexible inventory management for Symfony2 applications.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusInventoryBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_inventory';
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaces()
    {
        return array(
            'Sylius\Component\Inventory\Model\InventoryUnitInterface' => 'sylius.model.inventory_unit.class',
            'Sylius\Component\Inventory\Model\StockableInterface'     => 'sylius.model.stockable.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityNamespace()
    {
        return 'Sylius\Component\Inventory\Model';
    }
}
