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

namespace spec\Sylius\Component\Core\Payment\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Exception\NotProvidedOrderPaymentException;
use Sylius\Component\Core\Payment\Provider\OrderPaymentProviderInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

final class OrderPaymentProviderSpec extends ObjectBehavior
{
    function let(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $this->beConstructedWith(
            $defaultPaymentMethodResolver,
            $paymentFactory,
            $stateMachine,
        );
    }

    function it_implements_order_payment_provider_interface(): void
    {
        $this->shouldImplement(OrderPaymentProviderInterface::class);
    }

    function it_provides_payment_in_configured_state_with_payment_method_from_last_cancelled_payment(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $lastCancelledPayment,
        PaymentInterface $newPayment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn($lastCancelledPayment);

        $lastCancelledPayment->getMethod()->willReturn($paymentMethod);

        $paymentFactory->createWithAmountAndCurrencyCode(1000, 'USD')->willReturn($newPayment);
        $defaultPaymentMethodResolver->getDefaultPaymentMethod($newPayment)->willReturn($paymentMethod);

        $newPayment->setMethod($paymentMethod)->shouldBeCalled();
        $newPayment->getState()->willReturn(PaymentInterface::STATE_CART);
        $newPayment->setOrder($order)->shouldBeCalled();

        $stateMachine
            ->getTransitionToState($newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE)
        ;
        $stateMachine
            ->apply($newPayment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE)
            ->shouldBeCalled()
        ;

        $this
            ->provideOrderPayment($order, PaymentInterface::STATE_NEW)
            ->shouldReturn($newPayment)
        ;
    }

    function it_provides_payment_in_configured_state_with_payment_method_from_last_failed_payment(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $lastFailedPayment,
        PaymentInterface $newPayment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn($lastFailedPayment);

        $lastFailedPayment->getMethod()->willReturn($paymentMethod);

        $paymentFactory->createWithAmountAndCurrencyCode(1000, 'USD')->willReturn($newPayment);
        $defaultPaymentMethodResolver->getDefaultPaymentMethod($newPayment)->willReturn($paymentMethod);

        $newPayment->setMethod($paymentMethod)->shouldBeCalled();
        $newPayment->getState()->willReturn(PaymentInterface::STATE_CART);
        $newPayment->setOrder($order)->shouldBeCalled();

        $stateMachine
            ->getTransitionToState($newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE)
        ;
        $stateMachine
            ->apply($newPayment,PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE)
            ->shouldBeCalled()
        ;

        $this
            ->provideOrderPayment($order, PaymentInterface::STATE_NEW)
            ->shouldReturn($newPayment)
        ;
    }

    function it_provides_payment_in_configured_state_with_default_payment_method(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $newPayment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);

        $paymentFactory->createWithAmountAndCurrencyCode(1000, 'USD')->willReturn($newPayment);
        $newPayment->setOrder($order)->shouldBeCalled();

        $defaultPaymentMethodResolver->getDefaultPaymentMethod($newPayment)->willReturn($paymentMethod);

        $newPayment->setMethod($paymentMethod)->shouldBeCalled();
        $newPayment->getState()->willReturn(PaymentInterface::STATE_CART);

        $stateMachine
            ->getTransitionToState($newPayment, PaymentTransitions::GRAPH, PaymentInterface::STATE_NEW)
            ->willReturn(PaymentTransitions::TRANSITION_CREATE)
        ;
        $stateMachine
            ->apply($newPayment,PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE)
            ->shouldBeCalled()
        ;

        $this->provideOrderPayment($order, PaymentInterface::STATE_NEW)->shouldReturn($newPayment);
    }

    function it_does_not_apply_any_transition_if_target_state_is_the_same_as_new_payment(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        PaymentInterface $newPayment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn(null);

        $paymentFactory->createWithAmountAndCurrencyCode(1000, 'USD')->willReturn($newPayment);
        $newPayment->setOrder($order)->shouldBeCalled();

        $defaultPaymentMethodResolver->getDefaultPaymentMethod($newPayment)->willReturn($paymentMethod);

        $newPayment->setMethod($paymentMethod)->shouldBeCalled();
        $newPayment->getState()->willReturn(PaymentInterface::STATE_NEW);

        $stateMachine->getTransitionToState(Argument::cetera())->shouldNotBeCalled();

        $this->provideOrderPayment($order, PaymentInterface::STATE_NEW)->shouldReturn($newPayment);
    }

    function it_throws_exception_if_payment_method_cannot_be_resolved_for_provided_payment(
        DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
        PaymentFactoryInterface $paymentFactory,
        OrderInterface $order,
        PaymentInterface $lastFailedPayment,
        PaymentInterface $newPayment,
    ): void {
        $order->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLastPayment(PaymentInterface::STATE_CANCELLED)->willReturn(null);
        $order->getLastPayment(PaymentInterface::STATE_FAILED)->willReturn($lastFailedPayment);

        $defaultPaymentMethodResolver
            ->getDefaultPaymentMethod($newPayment)
            ->willThrow(UnresolvedDefaultPaymentMethodException::class)
        ;

        $lastFailedPayment->getMethod()->willReturn(null);

        $paymentFactory->createWithAmountAndCurrencyCode(1000, 'USD')->willReturn($newPayment);

        $this
            ->shouldThrow(NotProvidedOrderPaymentException::class)
            ->during('provideOrderPayment', [$order, PaymentInterface::STATE_NEW])
        ;
    }
}
