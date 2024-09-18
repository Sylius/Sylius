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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
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

    function it_throws_an_exception_if_value_is_not_an_instance_of_update_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder('token'), new UpdateCartEmailNotAllowed()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_update_cart_email_not_allowed(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new UpdateCart('token'), new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $command = new UpdateCart(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$command, new UpdateCartEmailNotAllowed()])
        ;
    }

    function it_does_not_add_violation_if_the_customer_on_the_order_is_null(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);
        $command = new UpdateCart(email: 'shopuser@example.com', orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $order->getCustomer()->willReturn(null);

        $userContext->getUser()->willReturn($shopUser);
        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldNotBeCalled();

        $this->validate($command, new UpdateCartEmailNotAllowed());
    }

    function it_does_not_add_violation_if_the_email_is_the_same_as_the_one_in_the_order(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);
        $command = new UpdateCart(email: 'shopuser@example.com', orderTokenValue: 'token');

        $customer->getEmail()->willReturn('shopuser@example.com');
        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $order->getCustomer()->willReturn($customer);

        $userContext->getUser()->willReturn($shopUser);
        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldNotBeCalled();

        $this->validate($command, new UpdateCartEmailNotAllowed());
    }

    function it_adds_violation_if_the_user_is_logged_in_and_they_try_to_change_the_email(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);
        $command = new UpdateCart(email: 'changed_email@example.com', orderTokenValue: 'token');

        $shopUser->getCustomer()->willReturn($customer);

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $userContext->getUser()->willReturn($shopUser);

        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldBeCalled();

        $this->validate($command, new UpdateCartEmailNotAllowed());
    }

    function it_does_not_add_violation_if_user_is_not_logged_in(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        ShopUserInterface $shopUser,
    ): void {
        $this->initialize($executionContext);
        $command = new UpdateCart(email: 'customer@example.com', orderTokenValue: 'token');

        $shopUser->getCustomer()->willReturn($customer);

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $userContext->getUser()->shouldBeCalled()->willReturn(null);

        $executionContext->addViolation('sylius.checkout.email.not_changeable')->shouldNotBeCalled();

        $this->validate($command, new UpdateCartEmailNotAllowed());
    }
}
