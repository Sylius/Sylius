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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeChangedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class ChoosePaymentMethodHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        StateMachineInterface $stateMachine,
        PaymentMethodChangerInterface $paymentMethodChanger,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $paymentMethodRepository,
            $paymentRepository,
            $stateMachine,
            $paymentMethodChanger,
        );
    }

    function it_assigns_chosen_payment_method_to_specified_payment_while_checkout(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        StateMachineInterface $stateMachine,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $this->beConstructedWith($orderRepository, $paymentMethodRepository, $paymentRepository, $stateMachine, $paymentMethodChanger);

        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $cart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_SHIPPING_SELECTED);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->shouldBeCalled();

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $cart->getId()->willReturn('111');

        $paymentRepository->findOneByOrderId('123', '111')->willReturn($payment);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);

        $payment->setMethod($paymentMethod)->shouldBeCalled();

        $this($choosePaymentMethod)->shouldReturn($cart);
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentInterface $payment,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_order_cannot_have_payment_selected(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        PaymentInterface $payment,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachine->can('select_payment')->willReturn(false);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_payment_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        PaymentInterface $payment,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn(null);
        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);

        $payment->setMethod(Argument::type(PaymentMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_ordered_payment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $cart->getState()->willReturn(OrderInterface::STATE_CART);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->willReturn(true);

        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $cart->getId()->willReturn('111');

        $paymentRepository->findOneByOrderId('123', '111')->willReturn(null);

        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_payment')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$choosePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_payment_is_in_different_state_than_new(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        OrderInterface $cart,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $paymentMethodRepository->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])->willReturn($paymentMethod);

        $cart->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);
        $cart->getId()->willReturn('111');

        $paymentRepository->findOneByOrderId('123', '111')->willReturn($payment);

        $cart->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $payment->getState()->willReturn(PaymentInterface::STATE_CANCELLED);

        $this
            ->shouldThrow(PaymentMethodCannotBeChangedException::class)
            ->during('__invoke', [$choosePaymentMethod])
        ;
    }

    function it_assigns_chosen_payment_method_to_specified_payment_after_checkout(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderInterface $cart,
    ): void {
        $choosePaymentMethod = new ChoosePaymentMethod(
            orderTokenValue: 'ORDERTOKEN',
            paymentId: 123,
            paymentMethodCode: 'CASH_ON_DELIVERY_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $cart->getState()->willReturn(OrderInterface::STATE_NEW);

        $paymentMethodChanger->changePaymentMethod('CASH_ON_DELIVERY_METHOD', 123, $cart);

        $paymentMethodRepository
            ->findOneBy(['code' => 'CASH_ON_DELIVERY_METHOD'])
            ->willReturn(Argument::type(PaymentMethodInterface::class))
            ->shouldNotBeCalled()
        ;

        $this($choosePaymentMethod)->shouldReturn($cart);
    }
}
