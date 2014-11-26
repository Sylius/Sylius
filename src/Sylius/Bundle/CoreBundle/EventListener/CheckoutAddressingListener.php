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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Automatic set user's default addressing
*
 * @author Liverbool <nukboon@gmail.com>
 */
class CheckoutAddressingListener
{
    public function setUserAddressing(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        if (null === $user = $order->getUser()) {
            return;
        }

        if (null === $user->getShippingAddress()) {
            $user->setShippingAddress(clone $order->getShippingAddress());
        }

        if (null === $user->getBillingAddress()) {
            $user->setBillingAddress(clone $order->getBillingAddress());
        }
    }
}
