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
    public function __construct(
        private OrderPaymentProviderInterface $orderPaymentProvider,
        private string $targetState = PaymentInterface::STATE_CART,
        private ?OrderPaymentsRemoverInterface $orderPaymentsRemover = null,
        /** @var array<string> $unprocessableOrderStates */
        private array $unprocessableOrderStates = [],
    ) {
        if ($this->orderPaymentsRemover === null) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                'Not passing an $orderPaymentsRemover to %s constructor is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }
        if ([] === $this->unprocessableOrderStates) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                'Not passing an $unprocessableOrderStates to %s constructor is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }
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
        if (null !== $this->orderPaymentsRemover) {
            return $this->orderPaymentsRemover->canRemovePayments($order);
        }

        return 0 === $order->getTotal();
    }

    private function removePayments(OrderInterface $order): void
    {
        if (null !== $this->orderPaymentsRemover) {
            $this->orderPaymentsRemover->removePayments($order);

            return;
        }

        $removablePayments = $order->getPayments()->filter(function (PaymentInterface $payment): bool {
            return $payment->getState() === PaymentInterface::STATE_CART;
        });

        foreach ($removablePayments as $payment) {
            $order->removePayment($payment);
        }
    }

    private function cannotBeProcessed(OrderInterface $order): bool
    {
        if ([] === $this->unprocessableOrderStates) {
            return OrderInterface::STATE_CANCELLED === $order->getState();
        }

        return in_array($order->getState(), $this->unprocessableOrderStates, true);
    }
}
