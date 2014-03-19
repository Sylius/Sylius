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

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\ShipmentFactoryInterface;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order shipping listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderShippingListener
{
    /**
     * Order shipments factory.
     *
     * @var ShipmentFactoryInterface
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
     * @param ShipmentFactoryInterface          $shipmentFactory
     * @param ShipmentProcessorInterface        $shippingProcessor
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(ShipmentFactoryInterface $shipmentFactory, ShipmentProcessorInterface $shippingProcessor, ShippingChargesProcessorInterface $shippingChargesProcessor)
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
        $this->shipmentFactory->createShipment(
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
            ShipmentInterface::STATE_ONHOLD,
            ShipmentInterface::STATE_CHECKOUT
        );
    }

    /**
     * Update shipment states after order is confirmed.
     *
     * @param GenericEvent $event
     */
    public function updateShipmentStatesReady(GenericEvent $event)
    {
        $this->shippingProcessor->updateShipmentStates(
            $this->getOrder($event)->getShipments(),
            ShipmentInterface::STATE_READY,
            ShipmentInterface::STATE_ONHOLD
        );
    }

    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order shipping listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        return $order;
    }
}
