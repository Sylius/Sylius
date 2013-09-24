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
use Sylius\Bundle\CoreBundle\OrderProcessing\ShippingChargesProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order shipping listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderShippingListener
{
    /**
     * Order shipping processor.
     *
     * @var ShippingChargesProcessorInterface
     */
    protected $shippingProcessor;

    /**
     * Constructor.
     *
     * @param ShippingChargesProcessorInterface $shippingProcessor
     */
    public function __construct(ShippingChargesProcessorInterface $shippingProcessor)
    {
        $this->shippingProcessor = $shippingProcessor;
    }

    /**
     * Get the order from event and run the shipping processor on it.
     *
     * @param GenericEvent $event
     */
    public function processOrderShippingCharges(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order shipping listener requires event subjct to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->shippingProcessor->applyShippingCharges($order);
    }
}
