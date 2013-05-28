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
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order taxation listener.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderShipmentListener
{
    /**
     * Shipment Factory
     *
     * @var ShipmentFactoryInterface
     */
    protected $shipmentFactory;

    /**
     * Constructor.
     *
     * @param ShipmentFactoryInterface $shipmentFactory
     */
    public function __construct(ShipmentFactoryInterface $shipmentFactory)
    {
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * Get the order from event and create the shipment.
     *
     * @param GenericEvent $event
     */
    public function createOrderShipment(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order shipment listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        if (!$event->hasArgument('shippingMethod')) {
            throw new \InvalidArgumentException(
                'Order shipment listener requires event argument "shippingMethod" to be defined'
            );
        }

        $this->shipmentFactory->createShipment($order, $event->getArgument('shippingMethod'));
    }
}
