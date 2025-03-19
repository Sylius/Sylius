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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;

final class ChooseShippingMethodHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        StateMachineInterface $stateMachine,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $shippingMethodRepository,
            $shipmentRepository,
            $eligibilityChecker,
            $stateMachine,
        );
    }

    function it_assigns_choosen_shipping_method_to_specified_shipment(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn($shipment);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->shouldBeCalled();

        $this($chooseShippingMethod)->shouldReturn($cart);
    }

    function it_throws_an_exception_if_shipping_method_is_not_eligible(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn($shipment);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShipmentInterface $shipment,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_order_cannot_have_shipping_selected(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        ShipmentInterface $shipment,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: null,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(false);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_shipping_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        ShipmentInterface $shipment,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_ordered_shipment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod(
            orderTokenValue: 'ORDERTOKEN',
            shipmentId: 123,
            shippingMethodCode: 'DHL_SHIPPING_METHOD',
        );

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachine->can($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn(null);

        $stateMachine->apply($cart, OrderCheckoutTransitions::GRAPH, 'select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }
}
