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
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderPaymentListener
{
    /**
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function updateOrderPayment(GenericEvent $event)
    {
        $order = $this->getOrder($event);

        if (!$order->hasPayments()) {
            throw new \InvalidArgumentException('Order payments cannot be empty.');
        }

        /** @var $payment PaymentInterface */
        $payment = $order->getPayments()->last();
        $payment->setCurrencyCode($order->getCurrencyCode());
        $payment->setAmount($order->getTotal());
    }

    /**
     * @param GenericEvent $event
     *
     * @return OrderInterface
     *
     * @throws UnexpectedTypeException
     */
    private function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, OrderInterface::class);
        }

        return $order;
    }
}
