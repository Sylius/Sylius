<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\EventListener;

use Sylius\Core\Model\CustomerInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Automatic set customer's default addressing
 *
 * @author Liverbool <nukboon@gmail.com>
 */
class CheckoutAddressingListener
{
    /**
     * @param GenericEvent $event
     */
    public function setCustomerAddressing(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                OrderInterface::class
            );
        }

        /** @var CustomerInterface $customer */
        if (null === $customer = $order->getCustomer()) {
            return;
        }

        if (null === $customer->getShippingAddress()) {
            $customer->setShippingAddress(clone $order->getShippingAddress());
        }

        if (null === $customer->getBillingAddress()) {
            $customer->setBillingAddress(clone $order->getBillingAddress());
        }
    }
}
