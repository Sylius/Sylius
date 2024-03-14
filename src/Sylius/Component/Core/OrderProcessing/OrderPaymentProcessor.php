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

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentProcessor implements OrderProcessorInterface
{
    /**
     * @param array<string> $unprocessableOrderStates
     */
    public function __construct(
        private OrderPaymentProviderInterface $orderPaymentProvider,
        private OrderPaymentsRemoverInterface $orderPaymentsRemover,
        private array $unprocessableOrderStates,
        private string $targetState = PaymentInterface::STATE_CART,
    ) {
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($this->cannotBeProcessed($order)) {
            return;
        }

        if ($this->canPaymentsBeRemoved($order)) {
            $this->removePayments($order);

            return;
        }

        $lastPayment = $order->getLastPayment($this->targetState);
        if (null !== $lastPayment) {
            $lastPayment->setCurrencyCode($order->getCurrencyCode());
            $lastPayment->setAmount($order->getTotal());

            return;
        }

        try {
            $newPayment = $this->orderPaymentProvider->provideOrderPayment($order, $this->targetState);
            $order->addPayment($newPayment);
        } catch (NotProvidedOrderPaymentException) {
            return;
        }
    }

    private function canPaymentsBeRemoved(OrderInterface $order): bool
    {
        return $this->orderPaymentsRemover->canRemovePayments($order);
    }

    private function removePayments(OrderInterface $order): void
    {
        $this->orderPaymentsRemover->removePayments($order);
    }

    private function cannotBeProcessed(OrderInterface $order): bool
    {
        return in_array($order->getState(), $this->unprocessableOrderStates, true);
    }
}
