<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\StateMachineCallback;

use Sylius\Core\Model\OrderInterface;
use Sylius\Shipping\Processor\ShipmentProcessorInterface;
use Sylius\Shipping\ShipmentTransitions;

/**
 * @author Liverbool <nukboon@gmail.com>
 */
class ShipmentStatesCallback
{
    /**
     * @var ShipmentProcessorInterface
     */
    protected $processor;

    /**
     * @param ShipmentProcessorInterface $processor
     */
    public function __construct(ShipmentProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    public function updateOrderShipmentStates(OrderInterface $order, $transition = ShipmentTransitions::SYLIUS_PREPARE)
    {
        if ($order->isBackorder()) {
            $transition = ShipmentTransitions::SYLIUS_BACKORDER;
        }

        $this->processor->updateShipmentStates($order->getShipments(), $transition);
    }
}
