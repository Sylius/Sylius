<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\StateResolver;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class OrderPaymentStateResolver implements OrderPaymentStateResolverInterface
{
    public function __construct(
        private WorkflowInterface $syliusOrderPaymentWorkflow,
    ) {
    }

    public function resolve(OrderInterface $order): void
    {
        $targetTransition = $this->getTargetTransition($order);

        if (null !== $targetTransition) {
            $this->applyTransition($order, $this->syliusOrderPaymentWorkflow, $targetTransition);
        }
    }

    private function applyTransition(OrderInterface $order, WorkflowInterface $syliusOrderPaymentWorkflow, string $transition): void
    {
        if ($syliusOrderPaymentWorkflow->can($order, $transition)) {
            $syliusOrderPaymentWorkflow->apply($order, $transition);
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
     * @return Collection|PaymentInterface[]
     *
     * @psalm-return Collection<array-key, PaymentInterface>
     */
    private function getPaymentsWithState(OrderInterface $order, string $state): Collection
    {
        return $order->getPayments()->filter(function (PaymentInterface $payment) use ($state) {
            return $state === $payment->getState();
        });
    }
}
