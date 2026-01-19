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
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChosenShippingMethodEligibilityValidatorTest extends TestCase
{
    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ShippingMethodsResolverInterface $shippingMethodsResolver;

    private ExecutionContextInterface&MockObject $executionContext;

    private ChosenShippingMethodEligibilityValidator $chosenShippingMethodEligibilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->shippingMethodsResolver = $this->createMock(ShippingMethodsResolverInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->chosenShippingMethodEligibilityValidator = new ChosenShippingMethodEligibilityValidator($this->shipmentRepository, $this->shippingMethodRepository, $this->shippingMethodsResolver);
        $this->chosenShippingMethodEligibilityValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->chosenShippingMethodEligibilityValidator);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfChosenShippingMethodEligibility(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );

        $this->chosenShippingMethodEligibilityValidator->validate($command, $invalidConstraint);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfChooseShippingMethodCommand(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->chosenShippingMethodEligibilityValidator->validate(new CompleteOrder('TOKEN'), new ChosenShippingMethodEligibility());
    }

    public function testAddsViolationIfChosenShippingMethodDoesNotMatchSupportedMethods(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShippingMethodInterface|MockObject $differentShippingMethodMock */
        $differentShippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );
        $this->shippingMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethodMock);
        $shippingMethodMock->expects(self::once())->method('getName')->willReturn('DHL');
        $this->shipmentRepository->expects(self::once())->method('find')->with('123')->willReturn($shipmentMock);
        $shipmentMock->expects(self::once())->method('getOrder')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn($shippingAddressMock);
        $this->shippingMethodsResolver->expects(self::once())->method('getSupportedMethods')->with($shipmentMock)->willReturn([$differentShippingMethodMock]);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.shipping_method.not_available', ['%name%' => 'DHL'])
        ;
        $this->chosenShippingMethodEligibilityValidator->validate($command, new ChosenShippingMethodEligibility());
    }

    public function testDoesNotAddViolationIfChosenShippingMethodMatchesSupportedMethods(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );
        $this->shippingMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethodMock);
        $this->shipmentRepository->expects(self::once())->method('find')->with('123')->willReturn($shipmentMock);
        $shipmentMock->expects(self::once())->method('getOrder')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn($shippingAddressMock);
        $this->shippingMethodsResolver->expects(self::once())->method('getSupportedMethods')->with($shipmentMock)->willReturn([$shippingMethodMock]);
        $this->executionContext->expects(self::never())->method('addViolation')
        ;
        $this->chosenShippingMethodEligibilityValidator->validate($command, new ChosenShippingMethodEligibility());
    }

    public function testAddsAViolationIfGivenShippingMethodIsNull(): void
    {
        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'SHIPPING_METHOD_CODE'])
            ->willReturn(null);

        $this->shipmentRepository
            ->expects(self::never())
            ->method('find');

        $this->executionContext
            ->expects(self::once())
            ->method('addViolation')
            ->with('sylius.shipping_method.not_found', ['%code%' => 'SHIPPING_METHOD_CODE']);

        $this->chosenShippingMethodEligibilityValidator->validate(
            $command,
            new ChosenShippingMethodEligibility(),
        );
    }

    public function testAddsViolationIfOrderIsNotAddressed(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );
        $this->shippingMethodRepository->expects(self::once())->method('findOneBy')->with(['code' => 'SHIPPING_METHOD_CODE'])->willReturn($shippingMethodMock);
        $this->shipmentRepository->expects(self::once())->method('find')->with('123')->willReturn($shipmentMock);
        $shipmentMock->expects(self::once())->method('getOrder')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn(null);
        $this->shippingMethodsResolver->expects(self::once())->method('getSupportedMethods')->with($shipmentMock)->willReturn([$shippingMethodMock]);
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.shipping_method.shipping_address_not_found')
        ;
        $this->chosenShippingMethodEligibilityValidator->validate($command, new ChosenShippingMethodEligibility());
    }

    public function testAddsViolationIfOrderShipmentIsNotFound(): void
    {
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);

        $command = new ChooseShippingMethod(
            orderTokenValue: 'ORDER_TOKEN',
            shippingMethodCode: 'SHIPPING_METHOD_CODE',
            shipmentId: 123,
        );

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'SHIPPING_METHOD_CODE'])
            ->willReturn($shippingMethodMock);

        $this->shipmentRepository
            ->expects(self::once())
            ->method('find')
            ->with('123')
            ->willReturn(null);

        $this->executionContext
            ->expects(self::once())
            ->method('addViolation')
            ->with('sylius.shipment.not_found');

        $this->chosenShippingMethodEligibilityValidator->validate(
            $command,
            new ChosenShippingMethodEligibility(),
        );
    }
}
