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
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmpty;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderNotEmptyValidatorSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_order_token_value_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_order_not_empty(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, new OrderNotEmpty()])
        ;
    }

    function it_adds_violation_if_the_order_has_no_items(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getItems()->willReturn(new ArrayCollection());
        $executionContext->addViolation('sylius.order.not_empty')->shouldBeCalled();

        $this->validate($value, new OrderNotEmpty());
    }

    function it_does_not_add_violation_if_the_order_has_items(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $executionContext->addViolation('sylius.order.not_empty')->shouldNotBeCalled();

        $this->validate($value, new OrderNotEmpty());
    }
}
