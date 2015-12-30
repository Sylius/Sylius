<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Inventory\Model\StockLocationAwareInterface;
use Sylius\Component\Order\Model\OrderAwareInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

/**
 * Shipment interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentInterface extends BaseShipmentInterface, OrderAwareInterface, StockLocationAwareInterface
{
}
