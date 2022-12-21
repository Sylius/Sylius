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

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\OrderItemUnit as BaseOrderItemUnit;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Webmozart\Assert\Assert;

class OrderItemUnit extends BaseOrderItemUnit implements OrderItemUnitInterface
{
    use TimestampableTrait;

    /** @var ShipmentInterface|null */
    protected $shipment;

    public function __construct(OrderItemInterface $orderItem)
    {
        parent::__construct($orderItem);

        $this->createdAt = new \DateTime();
    }

    public function getShipment(): ?BaseShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(?BaseShipmentInterface $shipment): void
    {
        Assert::nullOrIsInstanceOf($shipment, ShipmentInterface::class);
        $this->shipment = $shipment;
    }

    public function getStockable(): ?StockableInterface
    {
        Assert::isInstanceOf($this->orderItem, OrderItemInterface::class);

        return $this->orderItem->getVariant();
    }

    public function getShippable(): ?ShippableInterface
    {
        Assert::isInstanceOf($this->orderItem, OrderItemInterface::class);

        return $this->orderItem->getVariant();
    }

    public function getTaxTotal(): int
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }

        return $taxTotal;
    }
}
