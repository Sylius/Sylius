<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentProcessor implements PaymentProcessorInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    protected $paymentFactory;

    /**
     * @param PaymentFactoryInterface $paymentFactory
     */
    public function __construct(PaymentFactoryInterface $paymentFactory)
    {
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function processOrderPayments(OrderInterface $order)
    {
        if ($payment = $order->getLastPayment(PaymentInterface::STATE_NEW)) {
            $payment->setAmount($order->getTotal());

            return;
        }

        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createWithAmountAndCurrencyCode($order->getTotal(), $order->getCurrencyCode());
        $this->setPaymentMethodIfNeeded($order, $payment);

        $order->addPayment($payment);
    }

    /**
     * {@inheritdoc}
     */
    private function setPaymentMethodIfNeeded(OrderInterface $order, PaymentInterface $payment)
    {
        $lastPayment = $this->getLastPayment($order);

        if (null === $lastPayment) {
            return;
        }

        $payment->setMethod($lastPayment->getMethod());
    }

    /**
     * @param OrderInterface $order
     *
     * @return null|PaymentInterface
     */
    private function getLastPayment(OrderInterface $order)
    {
        $lastPayment = $order->getLastPayment(PaymentInterface::STATE_CANCELLED);

        if (null === $lastPayment) {
            $lastPayment = $order->getLastPayment(PaymentInterface::STATE_FAILED);
        }

        return $lastPayment;
    }
}
