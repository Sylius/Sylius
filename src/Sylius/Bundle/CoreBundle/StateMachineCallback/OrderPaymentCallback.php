<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentCallback
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var PaymentProcessorInterface
     */
    protected $paymentProcessor;

    /**
     * @param FactoryInterface $factory
     * @param PaymentProcessorInterface $paymentProcessor
     */
    public function __construct(FactoryInterface $factory, PaymentProcessorInterface $paymentProcessor)
    {
        $this->factory = $factory;
        $this->paymentProcessor = $paymentProcessor;
    }

    public function updateOrderOnPayment(PaymentInterface $payment)
    {
        /** @var $order OrderInterface */
        $order = $payment->getOrder();
        if (null === $order) {
            throw new \RuntimeException(sprintf('Cannot retrieve Order from Payment with id %s', $payment->getId()));
        }

        $total = 0;
        if (PaymentInterface::STATE_COMPLETED === $payment->getState()) {
            $payments = $order->getPayments()->filter(function (PaymentInterface $payment) {
                return PaymentInterface::STATE_COMPLETED === $payment->getState();
            });

            if ($payments->count() === $order->getPayments()->count()) {
                $order->setPaymentState(PaymentInterface::STATE_COMPLETED);
            }

            $total += $payment->getAmount();
        } else {
            $order->setPaymentState($payment->getState());
        }

        if ($total >= $order->getTotal()) {
            $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CONFIRM, true);
        }
    }

    public function processOrderPayments(PaymentInterface $payment)
    {
        $this->paymentProcessor->processOrderPayments($payment->getOrder());
    }
}
