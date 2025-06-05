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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShipped;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ShipmentAlreadyShippedValidatorTest extends TestCase
{
    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private ShipmentAlreadyShippedValidator $shipmentAlreadyShippedValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->shipmentAlreadyShippedValidator = new ShipmentAlreadyShippedValidator($this->shipmentRepository);
        $this->shipmentAlreadyShippedValidator->initialize($this->executionContext);
    }

    public function testAddsViolationIfShipmentStatusIsShipped(): void
    {
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $constraint = new ShipmentAlreadyShipped();
        $shipShipment = new ShipShipment(shipmentId: 123);
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn($shipmentMock);
        $shipmentMock->expects(self::once())->method('getState')->willReturn(OrderShippingStates::STATE_SHIPPED);
        $this->executionContext->expects(self::once())->method('addViolation')->with($constraint->message);
        $this->shipmentAlreadyShippedValidator->validate($shipShipment, $constraint);
    }

    public function testDoesNothingIfShipmentStatusIsDifferentThanShipped(): void
    {
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $constraint = new ShipmentAlreadyShipped();
        $shipShipment = new ShipShipment(shipmentId: 123);
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn($shipmentMock);
        $shipmentMock->expects(self::once())->method('getState')->willReturn(OrderShippingStates::STATE_CART);
        $this->executionContext->expects(self::never())->method('addViolation')->with($constraint->message);
        $this->shipmentAlreadyShippedValidator->validate($shipShipment, $constraint);
    }
}
