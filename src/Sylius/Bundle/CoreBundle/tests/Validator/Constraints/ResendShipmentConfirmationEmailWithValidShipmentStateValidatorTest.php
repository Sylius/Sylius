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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Command\ResendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentState;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentStateValidator;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendShipmentConfirmationEmailWithValidShipmentStateValidatorTest extends TestCase
{
    private MockObject&RepositoryInterface $shipmentRepository;

    private ExecutionContextInterface&MockObject $context;

    private ResendShipmentConfirmationEmailWithValidShipmentStateValidator $validator;

    protected function setUp(): void
    {
        $this->shipmentRepository = $this->createMock(RepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ResendShipmentConfirmationEmailWithValidShipmentStateValidator($this->shipmentRepository);
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItThrowsExceptionIfConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new ResendShipmentConfirmationEmail(123), $this->createMock(Constraint::class));
    }

    public function testItThrowsExceptionIfValueIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(new \stdClass(), new ResendShipmentConfirmationEmailWithValidShipmentState());
    }

    public function testItDoesNothingWhenStateIsValid(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getState')->willReturn(ShipmentInterface::STATE_SHIPPED);

        $this->shipmentRepository
            ->method('findOneBy')
            ->with(['id' => 2])
            ->willReturn($shipment)
        ;

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate(new ResendShipmentConfirmationEmail(2), new ResendShipmentConfirmationEmailWithValidShipmentState());
    }

    public function testItDoesNothingWhenShipmentDoesNotExist(): void
    {
        $this->shipmentRepository
            ->method('findOneBy')
            ->with(['id' => 2])
            ->willReturn(null)
        ;

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate(new ResendShipmentConfirmationEmail(2), new ResendShipmentConfirmationEmailWithValidShipmentState());
    }

    public function testItAddsViolationWhenShipmentHasInvalidState(): void
    {
        $constraint = new ResendShipmentConfirmationEmailWithValidShipmentState();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getState')->willReturn(ShipmentInterface::STATE_CANCELLED);

        $this->shipmentRepository
            ->method('findOneBy')
            ->with(['id' => 2])
            ->willReturn($shipment)
        ;

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with($constraint->message, ['%state%' => ShipmentInterface::STATE_CANCELLED])
        ;

        $this->validator->validate(new ResendShipmentConfirmationEmail(2), $constraint);
    }
}
