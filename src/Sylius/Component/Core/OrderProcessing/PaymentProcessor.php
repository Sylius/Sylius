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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * Payment processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentProcessor implements PaymentProcessorInterface
{
    /**
     * Payment repository.
     *
     * @var FactoryInterface
     */
    protected $paymentFactory;

    /**
     * Payment Manager
     *
     * @var ObjectManager
     */
    protected $paymentManager;

    /**
     * Constructor.
     *
     * @param FactoryInterface $paymentFactory
     */
    public function __construct(FactoryInterface $paymentFactory, ObjectManager $paymentManager)
    {
        $this->paymentFactory = $paymentFactory;
        $this->paymentManager = $paymentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createPayment(OrderInterface $order)
    {
        $this->updateExistingPaymentsStates($order);

        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createNew();
        $payment->setCurrency($order->getCurrency());
        $payment->setAmount($order->getTotal());

        $order->addPayment($payment);

        return $payment;
    }

    /**
     * @param OrderInterface $order
     */
    private function updateExistingPaymentsStates(OrderInterface $order)
    {
        foreach ($order->getPayments() as $payment) {
            $this->cancelPaymentStateIfNotStarted($payment);
        }

        $this->paymentManager->flush();
    }

    /**
     * @param PaymentInterface $payment
     */
    private function cancelPaymentStateIfNotStarted(PaymentInterface $payment)
    {
        if (PaymentInterface::STATE_NEW === $payment->getState()) {
            $payment->setState(PaymentInterface::STATE_CANCELLED);
        }
    }
}
