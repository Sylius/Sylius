<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Webmozart\Assert\Assert;

class Shipment extends BaseShipment implements ShipmentInterface
{
    /** @var BaseOrderInterface|null */
    protected $order;

    public function getOrder(): ?BaseOrderInterface
    {
        return $this->order;
    }

    public function setOrder(?BaseOrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getShippingUnitTotal(): int
    {
        return array_sum(array_map(function (ShipmentUnitInterface $shipmentUnit) {
            Assert::isInstanceOf($shipmentUnit, OrderItemUnitInterface::class);

            return $shipmentUnit->getTotal();
        }, $this->units->toArray()));
    }
}
