<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Order\Model\OrderAwareInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

interface ShipmentInterface extends BaseShipmentInterface, OrderAwareInterface, AdjustableInterface
{
    /**
     * @return OrderInterface|null
     */
    public function getOrder(): ?BaseOrderInterface;
}
