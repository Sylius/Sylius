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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Message\ResendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentState;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendShipmentConfirmationEmailWithValidShipmentStateValidatorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $shipmentRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($shipmentRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_resend_order_confirmation_email_with_valid_order_state(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [new ResendShipmentConfirmationEmail(123), $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_resend_shipment_confirmation(): void
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [new \stdClass(), new ResendShipmentConfirmationEmailWithValidShipmentState()])
        ;
    }

    function it_does_nothing_if_the_state_is_valid(
        RepositoryInterface $shipmentRepository,
        ExecutionContextInterface $context,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRepository->findOneBy(['id' => 2])->willReturn($shipment);
        $shipment->getState()->willReturn(ShipmentInterface::STATE_SHIPPED);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();

        $this->validate(
            new ResendShipmentConfirmationEmail(2),
            new ResendShipmentConfirmationEmailWithValidShipmentState(),
        );
    }

    function it_adds_a_violation_if_order_has_invalid_state(
        RepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
        ExecutionContextInterface $context,
    ): void {
        $constraint = new ResendShipmentConfirmationEmailWithValidShipmentState();
        $shipmentRepository->findOneBy(['id' => 2])->willReturn($shipment);
        $shipment->getState()->willReturn(ShipmentInterface::STATE_CANCELLED);

        $context
            ->addViolation($constraint->message, ['%state%' => ShipmentInterface::STATE_CANCELLED])
            ->shouldBeCalled()
        ;

        $this->validate(new ResendShipmentConfirmationEmail(2), $constraint);
    }
}
