<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Updater;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderPaymentStateUpdater implements OrderUpdaterInterface
{
    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(FactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function update(OrderInterface $order)
    {
        foreach ($order->getPayments() as $payment) {
            if ($payment->getState() !== PaymentInterface::STATE_REFUNDED) {
                return;
            }
        }

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
        $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND);
    }
}
