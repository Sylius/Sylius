<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingListener
{
    /**
     * @var OrderShipmentProcessorInterface
     */
    protected $orderShipmentProcessor;

    /**
     * @var ShipmentProcessorInterface
     */
    protected $shippingProcessor;

    /**
     * @param OrderShipmentProcessorInterface $orderShipmentProcessor
     * @param ShipmentProcessorInterface $shippingProcessor
     */
    public function __construct(
        OrderShipmentProcessorInterface $orderShipmentProcessor,
        ShipmentProcessorInterface $shippingProcessor
    ) {
        $this->orderShipmentProcessor = $orderShipmentProcessor;
        $this->shippingProcessor = $shippingProcessor;
    }

    /**
     * @param GenericEvent $event
     */
    public function processOrderShipments(GenericEvent $event)
    {
        $this->orderShipmentProcessor->processOrderShipment($this->getOrder($event));
    }

    /**
     * @param GenericEvent $event
     */
    public function updateShipmentStatesOnhold(GenericEvent $event)
    {
        $this->shippingProcessor->updateShipmentStates(
            $this->getOrder($event)->getShipments(),
            ShipmentTransitions::SYLIUS_HOLD
        );
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderInterface
     */
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, OrderInterface::class);
        }

        return $order;
    }
}
