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

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockMovementInterface;

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
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            InventoryUnitInterface::class => 'sylius.model.inventory_unit.class',
            StockableInterface::class     => 'sylius.model.stockable.class',
            StockItemInterface::class     => 'sylius.model.stock_item.class',
            StockLocationInterface::class => 'sylius.model.stock_location.class',
            StockMovementInterface::class => 'sylius.model.stock_movement.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Inventory\Model';
    }
}
