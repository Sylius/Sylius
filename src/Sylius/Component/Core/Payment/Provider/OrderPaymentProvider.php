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

namespace Sylius\Component\Core\Payment\Provider;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentProvider implements OrderPaymentProviderInterface
{
    public function __construct(
        private DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        private PaymentFactoryInterface $paymentFactory,
        private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
    ) {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the third argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function provideOrderPayment(OrderInterface $order, string $targetState): ?PaymentInterface
    {
        /** @var PaymentInterface $payment */
        $payment = $this->paymentFactory->createWithAmountAndCurrencyCode(
            $order->getTotal(),
            $order->getCurrencyCode(),
        );

        $paymentMethod = $this->getDefaultPaymentMethod($payment, $order);
        $lastPayment = $this->getLastPayment($order);

        if (null !== $lastPayment) {
            $paymentMethod = $lastPayment->getMethod();
        }

        if (null === $paymentMethod) {
            throw new NotProvidedOrderPaymentException();
        }

        $payment->setMethod($paymentMethod);
        $this->applyRequiredTransition($payment, $targetState);

        return $payment;
    }

    private function getLastPayment(OrderInterface $order): ?PaymentInterface
    {
        $lastCancelledPayment = $order->getLastPayment(PaymentInterface::STATE_CANCELLED);
        if (null !== $lastCancelledPayment) {
            return $lastCancelledPayment;
        }

        return $order->getLastPayment(PaymentInterface::STATE_FAILED);
    }

    private function getDefaultPaymentMethod(PaymentInterface $payment, OrderInterface $order): ?PaymentMethodInterface
    {
        try {
            $payment->setOrder($order);
            $paymentMethod = $this->defaultPaymentMethodResolver->getDefaultPaymentMethod($payment);
            Assert::isInstanceOf($paymentMethod, PaymentMethodInterface::class);

            return $paymentMethod;
        } catch (UnresolvedDefaultPaymentMethodException) {
            return null;
        }
    }

    private function applyRequiredTransition(PaymentInterface $payment, string $targetState): void
    {
        if ($targetState === $payment->getState()) {
            return;
        }

        $stateMachine = $this->getStateMachine();

        $targetTransition = $stateMachine->getTransitionToState($payment, PaymentTransitions::GRAPH, $targetState);
        if (null !== $targetTransition) {
            $stateMachine->apply($payment, PaymentTransitions::GRAPH, $targetTransition);
        }
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
