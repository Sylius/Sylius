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
use Sylius\Bundle\CoreBundle\OrderProcessing\StateResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Sylius\Bundle\ShippingBundle\Processor\ShipmentProcessorInterface;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;

/**
 * Order inventory processing listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderStateListener
{
    /**
     * States resolver.
     *
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
     * Constructor.
     *
     * @param StateResolverInterface     $stateResolver
     * @param ShipmentProcessorInterface $shippingProcessor
     */
    public function __construct(StateResolverInterface $stateResolver, ShipmentProcessorInterface $shippingProcessor)
    {
        $this->stateResolver = $stateResolver;
        $this->shippingProcessor = $shippingProcessor;
    }

    /**
     * Get the order from event and run the inventory processor on it.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function resolveOrderStates(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventory listener requires event subject to be an instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->stateResolver->resolvePaymentState($order);
        $this->stateResolver->resolveShippingState($order);
    }

    public function release(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventory listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderItemInterface"'
            );
        }

        $order->setState(OrderInterface::STATE_CART);
        $order->setPaymentState(PaymentInterface::STATE_VOID);
        $order->getPayment()->setState($order->getPaymentState());
        $order->setShippingState(ShipmentInterface::STATE_CHECKOUT);
        $this->shippingProcessor->updateShipmentStates($order->getShipments(), $order->getShippingState());
    }
}
