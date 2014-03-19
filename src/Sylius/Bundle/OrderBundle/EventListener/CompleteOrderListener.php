<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventListener;

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Set an Order as completed
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CompleteOrderListener
{
    /**
     * Set an Order as completed
     *
     * @param GenericEvent $event
     */
    public function completeOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Event subject must implement Sylius\\Bundle\\OrderBundle\\Model\\OrderInterface, %s given.',
                is_object($order) ? get_class($order) : gettype($order)
            ));
        }

        $order->complete();
    }
}
