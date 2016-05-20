<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Shipping\ShipmentTransitions;

/**
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
class OrderItemUnitCallback
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param OrderItemUnitInterface $unit
     */
    public function updateShipmentStateOnInventoryRestock(OrderItemUnitInterface $unit)
    {
        if (!$shipment = $unit->getShipment()) {
            return;
        }

        $units = $shipment->getUnits();

        foreach ($units as $unit) {
            if (!in_array($unit->getInventoryState(), [
                OrderItemUnitInterface::STATE_ONHOLD,
                OrderItemUnitInterface::STATE_SOLD,
            ])) {
                return;
            }
        }

        $this->factory->get($shipment, ShipmentTransitions::GRAPH)->apply(ShipmentTransitions::SYLIUS_PREPARE);
    }
}
