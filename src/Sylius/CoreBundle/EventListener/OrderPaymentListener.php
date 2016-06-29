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

use Sylius\Core\Model\OrderInterface;
use Sylius\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Payment\Model\PaymentInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderPaymentListener
{
    /**
     * @var PaymentProcessorInterface
     */
    protected $paymentProcessor;

    /**
     * @param PaymentProcessorInterface $paymentProcessor
     */
    public function __construct(PaymentProcessorInterface $paymentProcessor)
    {
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * @param GenericEvent $event
     */
    public function createOrderPayment(GenericEvent $event)
    {
        $this->paymentProcessor->processOrderPayments($this->getOrder($event));
    }

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
    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, OrderInterface::class);
        }

        return $order;
    }
}
