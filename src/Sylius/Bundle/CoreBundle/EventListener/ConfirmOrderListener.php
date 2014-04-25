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
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ConfirmOrderListener
{
    /**
     * Set an Order as completed
     *
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function confirmOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Order\Model\OrderInterface'
            );
        }

        $order->setState(OrderInterface::STATE_CONFIRMED);
    }
}
