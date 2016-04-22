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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Shipping\ShipmentTransitions;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderShipmentCallback
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
     * @param OrderInterface $order
     */
    public function updateOrderShippingState(OrderInterface $order)
    {
        foreach ($order->getShipments() as $shipment) {
            if ($this->factory->get($shipment, ShipmentTransitions::GRAPH)->can(ShipmentTransitions::SYLIUS_SHIP)) {
                return;
            }
        }

        $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_SHIP, true);
    }
}
