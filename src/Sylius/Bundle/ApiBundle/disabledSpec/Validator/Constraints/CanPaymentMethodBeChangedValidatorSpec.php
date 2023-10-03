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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChanged;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CanPaymentMethodBeChangedValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith($orderRepository);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_change_payment_method_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new CanPaymentMethodBeChanged()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_cannot_change_payment_method_for_cancelled_order(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new ChangePaymentMethod('code'), $constraint])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $command = new ChangePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $orderRepository->findOneByTokenValue('ORDER_TOKEN')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$command, new CanPaymentMethodBeChanged()])
        ;
    }

    function it_adds_violation_if_order_is_cancelled(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
    ): void {
        $command = new ChangePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $orderRepository->findOneByTokenValue('ORDER_TOKEN')->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);

        $executionContext
            ->addViolation('sylius.payment_method.cannot_change_payment_method_for_cancelled_order')
            ->shouldBeCalled()
        ;

        $this->validate($command, new CanPaymentMethodBeChanged());
    }

    function it_does_nothing_if_order_is_not_cancelled(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
    ): void {
        $command = new ChangePaymentMethod('PAYMENT_METHOD_CODE');
        $command->setOrderTokenValue('ORDER_TOKEN');
        $command->setSubresourceId('123');

        $orderRepository->findOneByTokenValue('ORDER_TOKEN')->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);

        $executionContext
            ->addViolation('sylius.payment_method.cannot_change_payment_method_for_cancelled_order')
            ->shouldNotBeCalled()
        ;

        $this->validate($command, new CanPaymentMethodBeChanged());
    }
}
