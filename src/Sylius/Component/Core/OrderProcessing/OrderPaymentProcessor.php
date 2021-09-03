<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentProcessor implements OrderProcessorInterface
{
    private OrderPaymentProviderInterface $orderPaymentProvider;

    private string $targetState;

    public function __construct(
        OrderPaymentProviderInterface $orderPaymentProvider,
        string $targetState = PaymentInterface::STATE_CART
    ) {
        $this->orderPaymentProvider = $orderPaymentProvider;
        $this->targetState = $targetState;
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return;
        }

        if (0 === $order->getTotal()) {
            $removablePayments = $order->getPayments()->filter(function (PaymentInterface $payment): bool {
                return $payment->getState() === OrderPaymentStates::STATE_CART;
            });

            foreach ($removablePayments as $payment) {
                $order->removePayment($payment);
            }

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
        } catch (NotProvidedOrderPaymentException $exception) {
            return;
        }
    }
}
