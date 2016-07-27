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
use Sylius\Component\Order\OrderTransitions;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderPaymentCallback
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function updateOrderOnPayment(PaymentInterface $payment)
    {
        /** @var $order OrderInterface */
        $order = $payment->getOrder();
        if (null === $order) {
            throw new \RuntimeException(sprintf('Cannot retrieve Order from Payment with id %s', $payment->getId()));
        }

        $payments = $order->getPayments()->filter(function (PaymentInterface $payment) {
            return PaymentInterface::STATE_COMPLETED === $payment->getState();
        });

        $completedPaymentTotal = 0;
        foreach ($payments as $payment) {
            $completedPaymentTotal += $payment->getAmount();
        }

        if ($completedPaymentTotal >= $order->getTotal()) {
            $this->factory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::TRANSITION_FULFILL, true);
        }
    }
}
