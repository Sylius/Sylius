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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ChooseShippingMethodHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private MockObject&StateMachineInterface $stateMachine;

    private ChooseShippingMethodHandler $handler;

    private MockObject&OrderInterface $cart;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private MockObject&ShipmentInterface $shipment;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->eligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->handler = new ChooseShippingMethodHandler(
            $this->orderRepository,
            $this->shippingMethodRepository,
            $this->shipmentRepository,
            $this->eligibilityChecker,
            $this->stateMachine,
        );
        $this->cart = $this->createMock(OrderInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->shipment = $this->createMock(ShipmentInterface::class);
    }

    public function testAssignsChoosenShippingMethodToSpecifiedShipment(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')
            ->willReturn(true);
        $this->shippingMethodRepository->expects(self::once())
            ->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])
            ->willReturn($this->shippingMethod);
        $this->cart->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->cart->method('getId')->willReturn('111');
        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '111')
            ->willReturn($this->shipment);
        $this->eligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->shipment, $this->shippingMethod)
            ->willReturn(true);
        $this->shipment->expects(self::once())->method('setMethod')->with($this->shippingMethod);
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::assertSame($this->cart, $this->handler->__invoke($chooseShippingMethod));
    }

    public function testThrowsAnExceptionIfShippingMethodIsNotEligible(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')
            ->willReturn(true);
        $this->shippingMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'DHL_SHIPPING_METHOD'])
            ->willReturn($this->shippingMethod);
        $this->cart->method('getShipments')->willReturn(new ArrayCollection([$this->shipment]));
        $this->cart->method('getId')->willReturn('111');
        $this->shipmentRepository->expects(self::once())
            ->method('findOneByOrderId')
            ->with('123', '111')
            ->willReturn($this->shipment);
        $this->eligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->shipment, $this->shippingMethod)
            ->willReturn(false);
        $this->shipment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderWithGivenTokenHasNotBeenFound(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn(null);
        $this->shipment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(ShippingMethodInterface::class));
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderCannotHaveShippingSelected(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->shippingMethodRepository->method('findOneBy')->with(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')
            ->willReturn(false);
        $this->shipment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfShippingMethodWithGivenCodeHasNotBeenFound(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')
            ->willReturn(true);
        $this->shippingMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'DHL_SHIPPING_METHOD'])
            ->willReturn(null);
        $this->shipment->expects(self::never())
            ->method('setMethod')
            ->with($this->isInstanceOf(ShippingMethodInterface::class));
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($chooseShippingMethod);
    }

    public function testThrowsAnExceptionIfOrderedShipmentHasNotBeenFound(): void
    {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->cart);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')
            ->willReturn(true);
        $this->shippingMethodRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['code' => 'DHL_SHIPPING_METHOD'])
            ->willReturn($this->shippingMethod);
        $this->cart->expects(self::once())->method('getId')->willReturn('111');
        $this->shipmentRepository->expects(self::once())->method('findOneByOrderId')->with('123', '111')->willReturn(null);
        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with($this->cart, OrderCheckoutTransitions::GRAPH, 'select_shipping');
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($chooseShippingMethod);
    }
}
