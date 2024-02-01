<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\Collection;
use SM\Factory\FactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentStateResolver implements StateResolverInterface
{
    public function __construct(private FactoryInterface|StateMachineInterface $stateMachineFactory)
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    FactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function resolve(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $targetTransition = $this->getTargetTransition($order);
        $stateMachine = $this->getStateMachine();

        if (null !== $targetTransition) {
            if ($stateMachine->can($order, OrderPaymentTransitions::GRAPH, $targetTransition)) {
                $stateMachine->apply($order, OrderPaymentTransitions::GRAPH, $targetTransition);
            }
        }
    }

    private function getTargetTransition(OrderInterface $order): ?string
    {
        $refundedPaymentTotal = 0;
        $refundedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_REFUNDED);

        foreach ($refundedPayments as $payment) {
            $refundedPaymentTotal += $payment->getAmount();
        }

        if (0 < $refundedPayments->count() && $refundedPaymentTotal >= $order->getTotal()) {
            return OrderPaymentTransitions::TRANSITION_REFUND;
        }

        if ($refundedPaymentTotal < $order->getTotal() && 0 < $refundedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND;
        }

        $completedPaymentTotal = 0;
        $completedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_COMPLETED);

        foreach ($completedPayments as $payment) {
            $completedPaymentTotal += $payment->getAmount();
        }

        if (
            (0 < $completedPayments->count() && $completedPaymentTotal >= $order->getTotal()) ||
            $order->getPayments()->isEmpty()
        ) {
            return OrderPaymentTransitions::TRANSITION_PAY;
        }

        if ($completedPaymentTotal < $order->getTotal() && 0 < $completedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_PAY;
        }

        // Authorized payments
        $authorizedPaymentTotal = 0;
        $authorizedPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_AUTHORIZED);

        foreach ($authorizedPayments as $payment) {
            $authorizedPaymentTotal += $payment->getAmount();
        }

        if (0 < $authorizedPayments->count() && $authorizedPaymentTotal >= $order->getTotal()) {
            return OrderPaymentTransitions::TRANSITION_AUTHORIZE;
        }

        if ($authorizedPaymentTotal < $order->getTotal() && 0 < $authorizedPaymentTotal) {
            return OrderPaymentTransitions::TRANSITION_PARTIALLY_AUTHORIZE;
        }

        // Processing payments
        $processingPaymentTotal = 0;
        $processingPayments = $this->getPaymentsWithState($order, PaymentInterface::STATE_PROCESSING);

        foreach ($processingPayments as $payment) {
            $processingPaymentTotal += $payment->getAmount();
        }

        if (0 < $processingPayments->count() && $processingPaymentTotal >= $order->getTotal()) {
            return OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT;
        }

        return null;
    }

    /**
     * @return Collection<array-key, PaymentInterface>
     */
    private function getPaymentsWithState(OrderInterface $order, string $state): Collection
    {
        /** @var Collection<array-key, PaymentInterface> $payments */
        $payments = $order->getPayments()->filter(function (BasePaymentInterface $payment) use ($state) {
            return $state === $payment->getState();
        });
        Assert::allIsInstanceOf($payments, PaymentInterface::class);

        return $payments;
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof FactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
