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

use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPaymentProcessor implements OrderProcessorInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    private $paymentFactory;

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
    public function process(OrderInterface $order)
    {
        /** @var CoreOrderInterface $order */
        Assert::isInstanceOf($order, CoreOrderInterface::class);

        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return;
        }

        $newPayment = $order->getLastPayment(PaymentInterface::STATE_NEW);
        if (null !== $newPayment) {
            $newPayment->setCurrencyCode($order->getCurrencyCode());
            $newPayment->setAmount($order->getTotal());

            return;
        }

        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createWithAmountAndCurrencyCode($order->getTotal(), $order->getCurrencyCode());
        $this->setPaymentMethodIfNeeded($order, $payment);

        $order->addPayment($payment);
    }

    /**
     * @param OrderInterface $order
     * @param PaymentInterface $payment
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
        return $order->getLastPayment(PaymentInterface::STATE_CANCELLED) ?: $order->getLastPayment(PaymentInterface::STATE_FAILED);
    }
}
