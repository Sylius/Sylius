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
use Sylius\Component\Core\OrderProcessing\OrderShipmentFactoryInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order shipping listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingListener
{
    /**
     * Order shipments factory.
     *
     * @var OrderShipmentFactoryInterface
     */
    protected $shipmentFactory;

    /**
     * Order shipping processor.
     *
     * @var ShipmentProcessorInterface
     */
    protected $shippingProcessor;

    /**
     * Order shipping charges processor.
     *
     * @var ShippingChargesProcessorInterface
     */
    protected $shippingChargesProcessor;

    /**
     * Constructor.
     *
     * @param OrderShipmentFactoryInterface     $shipmentFactory
     * @param ShipmentProcessorInterface        $shippingProcessor
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(OrderShipmentFactoryInterface $shipmentFactory, ShipmentProcessorInterface $shippingProcessor, ShippingChargesProcessorInterface $shippingChargesProcessor)
    {
        $this->shipmentFactory = $shipmentFactory;
        $this->shippingProcessor = $shippingProcessor;
        $this->shippingChargesProcessor = $shippingChargesProcessor;
    }

    /**
     * Get the order from event and create shipments.
     *
     * @param GenericEvent $event
     */
    public function processOrderShipments(GenericEvent $event)
    {
        $this->shipmentFactory->createForOrder(
            $this->getOrder($event)
        );
    }

    /**
     * Get the order from event and run the shipping processor on it.
     *
     * @param GenericEvent $event
     */
    public function processOrderShippingCharges(GenericEvent $event)
    {
        $this->shippingChargesProcessor->applyShippingCharges(
            $this->getOrder($event)
        );
    }

    /**
     * Update shipment states after order is created.
     *
     * @param GenericEvent $event
     */
    public function updateShipmentStatesOnhold(GenericEvent $event)
    {
        $this->shippingProcessor->updateShipmentStates(
            $this->getOrder($event)->getShipments(),
            ShipmentTransitions::SYLIUS_HOLD
        );
    }

    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, OrderInterface::class);
        }

        return $order;
    }
}
