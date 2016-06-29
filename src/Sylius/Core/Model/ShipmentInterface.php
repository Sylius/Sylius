<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Order\Model\OrderAwareInterface;
use Sylius\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShipmentInterface extends BaseShipmentInterface, OrderAwareInterface
{
}
