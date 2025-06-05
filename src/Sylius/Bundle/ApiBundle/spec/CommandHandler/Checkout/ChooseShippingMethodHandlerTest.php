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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChooseShippingMethodHandler;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;

final class ChooseShippingMethodHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var ShippingMethodRepositoryInterface|MockObject */
    private MockObject $shippingMethodRepositoryMock;

    /** @var ShipmentRepositoryInterface|MockObject */
    private MockObject $shipmentRepositoryMock;

    /** @var ShippingMethodEligibilityCheckerInterface|MockObject */
    private MockObject $eligibilityCheckerMock;

    /** @var StateMachineInterface|MockObject */
    private MockObject $stateMachineMock;

    private ChooseShippingMethodHandler $chooseShippingMethodHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->shippingMethodRepositoryMock = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->shipmentRepositoryMock = $this->createMock(ShipmentRepositoryInterface::class);
        $this->eligibilityCheckerMock = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->chooseShippingMethodHandler = new ChooseShippingMethodHandler($this->orderRepositoryMock, $this->shippingMethodRepositoryMock, $this->shipmentRepositoryMock, $this->eligibilityCheckerMock, $this->stateMachineMock);
    }

    public function testAssignsChoosenShippingMethodToSpecifiedShipment(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);
        $this->shippingMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethodMock);
        $cartMock->expects(self::once())->method('getShipments')->willReturn(new ArrayCollection([$shipmentMock]));
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->shipmentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn($shipmentMock);
        $this->eligibilityCheckerMock->expects(self::once())->method('isEligible')->with($shipmentMock, $shippingMethodMock)->willReturn(true);
        $shipmentMock->expects(self::once())->method('setMethod')->with($shippingMethodMock);
        $this->stateMachineMock->expects(self::once())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::assertSame($cartMock, $this($chooseShippingMethod));
    }

    public function testThrowsAnExceptionIfShippingMethodIsNotEligible(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);
        $this->shippingMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethodMock);
        $cartMock->expects(self::once())->method('getShipments')->willReturn(new ArrayCollection([$shipmentMock]));
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->shipmentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn($shipmentMock);
        $this->eligibilityCheckerMock->expects(self::once())->method('isEligible')->with($shipmentMock, $shippingMethodMock)->willReturn(false);
        $shipmentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        $this->expectException(InvalidArgumentException::class);
        $this->chooseShippingMethodHandler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);
        $shipmentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->expectException(InvalidArgumentException::class);
        $this->chooseShippingMethodHandler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderCannotHaveShippingSelected(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->shippingMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(false);
        $shipmentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        $this->expectException(InvalidArgumentException::class);
        $this->chooseShippingMethodHandler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfShippingMethodWithGivenCodeHasNotBeenFound(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);
        $this->shippingMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);
        $shipmentMock->expects(self::never())->method('setMethod')->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        $this->expectException(InvalidArgumentException::class);
        $this->chooseShippingMethodHandler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderedShipmentHasNotBeenFound(): void
    {
        /** @var OrderInterface|MockObject $cartMock */
        $cartMock = $this->createMock(OrderInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($cartMock);
        $this->stateMachineMock->expects(self::once())->method('can')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);
        $this->shippingMethodRepositoryMock->expects(self::once())->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethodMock);
        $cartMock->expects(self::once())->method('getId')->willReturn('111');
        $this->shipmentRepositoryMock->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn(null);
        $this->stateMachineMock->expects(self::never())->method('apply')->with($cartMock, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        $this->expectException(InvalidArgumentException::class);
        $this->chooseShippingMethodHandler->__invoke($chooseShippingMethod);
    }
}
