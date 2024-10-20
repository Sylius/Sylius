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

namespace Sylius\Component\Core\Payment\Refresher;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentRefresher implements OrderPaymentRefresherInterface
{
    public function __construct(
        private OrderPaymentProviderInterface $orderPaymentProvider,
        private OrderPaymentsRemoverInterface $orderPaymentsRemover,
    ) {
    }

    public function isPaymentRefreshingNeeded(OrderInterface $order): bool
    {
        $channel = $order->getChannel();
        $isAnyPaymentMethodDisabled = false;
        foreach ($order->getPayments() as $payment) {
            if ($payment->getMethod() !== null && $payment->getMethod()->isEnabled() === false) {
                $isAnyPaymentMethodDisabled = true;

                break;
            }
        }

        return $channel->isSkippingPaymentStepAllowed() && $isAnyPaymentMethodDisabled;
    }

    public function refreshPayments(OrderInterface $order, string $targetState): void
    {
        $this->orderPaymentsRemover->removePayments($order);

        try {
            $newPayment = $this->orderPaymentProvider->provideOrderPayment($order, $targetState);
            $order->addPayment($newPayment);
        } catch (NotProvidedOrderPaymentException) {
            return;
        }
    }
}
