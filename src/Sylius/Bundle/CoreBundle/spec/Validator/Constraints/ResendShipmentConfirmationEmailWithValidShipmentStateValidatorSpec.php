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
use Sylius\Bundle\CoreBundle\Message\ResendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentState;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ResendShipmentConfirmationEmailWithValidShipmentStateValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.resend_shipment_confirmation_email.invalid_shipment_state';

    function let(RepositoryInterface $shipmentRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($shipmentRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_resend_order_confirmation_email_with_valid_order_state(
        Constraint $constraint,
        ResendShipmentConfirmationEmail $value,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_does_nothing_if_the_state_is_valid(
        RepositoryInterface $shipmentRepository,
        ExecutionContextInterface $context,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRepository->findOneBy(['id' => 2])->willReturn($shipment);
        $shipment->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $this->validate(new ResendShipmentConfirmationEmail(2), new ResendShipmentConfirmationEmailWithValidShipmentState());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_order_has_invalid_state(
        RepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $shipmentRepository->findOneBy(['id' => 2])->willReturn($shipment);
        $shipment->getState()->willReturn(ShipmentInterface::STATE_CANCELLED);

        $context->addViolation(self::MESSAGE, ['%state%' => ShipmentInterface::STATE_CANCELLED])->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate(new ResendShipmentConfirmationEmail(2), new ResendShipmentConfirmationEmailWithValidShipmentState());
    }
}
