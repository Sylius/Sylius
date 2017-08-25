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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemUnit extends BaseOrderItemUnit implements OrderItemUnitInterface
{
    use TimestampableTrait;

    /**
     * @var ShipmentInterface
     */
    protected $shipment;

    /**
     * @param OrderItemInterface $orderItem
     */
    public function __construct(OrderItemInterface $orderItem)
    {
        parent::__construct($orderItem);

        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getShipment(): ?BaseShipmentInterface
    {
        return $this->shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function setShipment(?BaseShipmentInterface $shipment): void
    {
        $this->shipment = $shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockable(): ?StockableInterface
    {
        return $this->orderItem->getVariant();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippable(): ?ShippableInterface
    {
        return $this->orderItem->getVariant();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxTotal()
    {
        $taxTotal = 0;

        foreach ($this->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            $taxTotal += $taxAdjustment->getAmount();
        }

        return $taxTotal;
    }
}
