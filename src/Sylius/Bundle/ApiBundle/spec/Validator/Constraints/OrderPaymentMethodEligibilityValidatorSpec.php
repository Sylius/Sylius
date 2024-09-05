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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibility;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderPaymentMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_instance_of_complete_order(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new OrderPaymentMethodEligibility()])
        ;
    }

    function it_throws_an_exception_if_constraint_does_not_type_of_order_payment_method_eligibility(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new UpdateCart('token'), $constraint])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $constraint = new OrderPaymentMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_adds_violation_if_payment_is_not_available_anymore(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderPaymentMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $payment->getMethod()->willReturn($paymentMethod);

        $paymentMethod->getName()->willReturn('bank transfer');

        $paymentMethod->isEnabled()->willReturn(false);

        $executionContext
            ->addViolation(
                'sylius.order.payment_method_eligibility',
                ['%paymentMethodName%' => 'bank transfer'],
            )
            ->shouldBeCalled()
        ;

        $this->validate($value, $constraint);
    }

    function it_does_not_add_violation_if_payment_is_available(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderPaymentMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getPayments()->willReturn(new ArrayCollection([$payment->getWrappedObject()]));

        $payment->getMethod()->willReturn($paymentMethod);

        $paymentMethod->getName()->willReturn('bank transfer');
        $paymentMethod->isEnabled()->willReturn(true);

        $executionContext
            ->addViolation(
                'sylius.order.payment_method_eligibility',
                ['%paymentMethodName%' => 'bank transfer'],
            )
            ->shouldNotBeCalled()
        ;

        $this->validate($value, $constraint);
    }
}
