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
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShipped;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShipmentAlreadyShippedValidatorSpec extends ObjectBehavior
{
    function let(ShipmentRepositoryInterface $shipmentRepository, ExecutionContextInterface $executionContext): void
    {
        $this->beConstructedWith($shipmentRepository);

        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_shipment_status_is_shipped(
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new ShipmentAlreadyShipped();
        $shipShipment = new ShipShipment();
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->getState()->willReturn(OrderShippingStates::STATE_SHIPPED);

        $executionContext->addViolation($constraint->message)->shouldBeCalled();

        $this->validate($shipShipment, $constraint);
    }

    function it_does_nothing_if_shipment_status_is_different_than_shipped(
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
        ExecutionContextInterface $executionContext,
    ): void {
        $constraint = new ShipmentAlreadyShipped();
        $shipShipment = new ShipShipment();
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->getState()->willReturn(OrderShippingStates::STATE_CART);

        $executionContext->addViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($shipShipment, $constraint);
    }
}
