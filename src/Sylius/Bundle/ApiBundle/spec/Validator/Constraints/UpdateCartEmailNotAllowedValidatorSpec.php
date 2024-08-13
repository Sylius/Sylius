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
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UpdateCartEmailNotAllowed;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateCartEmailNotAllowedValidatorSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($orderRepository, $userContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_order_token_value_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new UpdateCart(), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_email_value_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new Order(), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_update_cart_email_not_allowed(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, new UpdateCartEmailNotAllowed()])
        ;
    }

    function it_adds_violation_if_the_user_is_logged_in(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        UserInterface $user,
        ExecutionContextInterface $executionContext,
        UserContextInterface $userContext,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(email: 'sylius@example.com', orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $userContext->getUser()->shouldBeCalled()->willReturn($user);

        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldBeCalled();

        $this->validate($value, new UpdateCartEmailNotAllowed());
    }

    function it_does_not_add_violation_if_user_is_not_logged_in(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $executionContext,
        UserContextInterface $userContext,
    ): void {
        $this->initialize($executionContext);

        $value = new UpdateCart(email: 'sylius@example.com', orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $userContext->getUser()->shouldBeCalled()->willReturn(null);

        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldNotBeCalled();

        $this->validate($value, new UpdateCartEmailNotAllowed());
    }
}
