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
        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());

        $order->addPayment($payment);
    }

    /**
     * {@inheritdoc}
     */
    public function createNewPaymentForOrder(OrderInterface $order)
    {
        $lastCancelledPayment = $order->getLastPayment(PaymentInterface::STATE_CANCELLED);
        $lastNewPayment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (!$lastNewPayment && $lastCancelledPayment) {
            $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());
            $payment->setMethod($lastCancelledPayment->getMethod());

            $order->addPayment($payment);
        }
    }
}
