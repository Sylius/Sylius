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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Message\ResendOrderConfirmationEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderState;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ResendOrderConfirmationEmailWithValidOrderStateValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.admin.resend_order_confirmation_email.invalid_order_state';

    function let(RepositoryInterface $orderRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($orderRepository, [OrderInterface::STATE_NEW]);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_resend_order_confirmation_email_with_valid_order_state(
        Constraint $constraint,
        ResendOrderConfirmationEmail $value,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_does_nothing_if_the_state_is_valid(
        RepositoryInterface $orderRepository,
        ExecutionContextInterface $context,
        OrderInterface $order,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_NEW);
        $this->validate(new ResendOrderConfirmationEmail('TOKEN'), new ResendOrderConfirmationEmailWithValidOrderState());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_order_has_invalid_state(
        RepositoryInterface $orderRepository,
        OrderInterface $order,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'TOKEN'])->willReturn($order);
        $order->getState()->willReturn(OrderInterface::STATE_FULFILLED);

        $context->addViolation(self::MESSAGE, ['%state%' => OrderInterface::STATE_FULFILLED])->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate(new ResendOrderConfirmationEmail('TOKEN'), new ResendOrderConfirmationEmailWithValidOrderState());
    }
}
