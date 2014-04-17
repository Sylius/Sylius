<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Processor;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentItemInterface;

/**
 * Shipment processor.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShipmentProcessor implements ShipmentProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function updateShipmentStates($shipments, $stateTo, $stateFrom = null)
    {
        if (!is_array($shipments) && !$shipments instanceof Collection) {
            throw new \InvalidArgumentException('Shipments value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        foreach ($shipments as $shipment) {
            if (!$shipment instanceof ShipmentInterface) {
                throw new UnexpectedTypeException($shipment, 'Sylius\Component\Shipping\Model\ShipmentInterface');
            }

            if (null === $stateFrom || $stateFrom === $shipment->getState()) {
                $shipment->setState($stateTo);
                $this->updateItemStates($shipment->getItems(), $stateTo, $stateFrom);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemStates($items, $stateTo, $stateFrom = null)
    {
        if (!is_array($items) && !$items instanceof Collection) {
            throw new \InvalidArgumentException('Inventory items value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        foreach ($items as $item) {
            if (!$item instanceof ShipmentItemInterface) {
                throw new UnexpectedTypeException($item, 'Sylius\Component\Shipping\Model\ShipmentItemInterface');
            }

            if (null === $stateFrom || $stateFrom === $item->getShippingState()) {
                $item->setShippingState($stateTo);
            }
        }
    }
}
