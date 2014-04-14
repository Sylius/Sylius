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

use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderShippingStates;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderProcessing\StateResolverInterface;
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Shipping\Processor\ShipmentProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Shipment listener.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShipmentListener
{
    /**
     * @var StateResolverInterface
     */
    protected $stateResolver;

    /**
     * Order shipping processor.
     *
     * @var ShipmentProcessorInterface
     */
    protected $shippingProcessor;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param StateResolverInterface     $stateResolver
     * @param ShipmentProcessorInterface $shippingProcessor
     * @param EventDispatcherInterface   $dispatcher
     */
    public function __construct(StateResolverInterface $stateResolver, ShipmentProcessorInterface $shippingProcessor, EventDispatcherInterface $dispatcher)
    {
        $this->stateResolver = $stateResolver;
        $this->shippingProcessor = $shippingProcessor;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Set shipment status to shipped.
     *
     * @param GenericEvent $event
     */
    public function ship(GenericEvent $event)
    {
        $shipment = $this->getShipment($event);
        $order = $shipment->getOrder();

        $this->shippingProcessor->updateShipmentStates(
            array($shipment),
            $shipment::STATE_SHIPPED
        );

        $this->stateResolver->resolveShippingState($order);

        if (OrderShippingStates::SHIPPED === $order->getShippingState()) {
            $this->dispatcher->dispatch(SyliusOrderEvents::PRE_SHIP, new GenericEvent($order));
        }
    }

    /**
     * Set shipment status to checkout.
     *
     * @param GenericEvent $event
     */
    public function releaseOrderShipments(GenericEvent $event)
    {
        $order = $this->getOrder($event);

        $this->shippingProcessor->updateShipmentStates(
            $order->getShipments(),
            ShipmentInterface::STATE_CHECKOUT,
            ShipmentInterface::STATE_ONHOLD
        );
    }

    private function getShipment(GenericEvent $event)
    {
        $shipment = $event->getSubject();

        if (!$shipment instanceof ShipmentInterface) {
            throw new UnexpectedTypeException(
                $shipment,
                'Sylius\Component\Core\Model\ShipmentInterface'
            );
        }

        return $shipment;
    }

    private function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        return $order;
    }
}
