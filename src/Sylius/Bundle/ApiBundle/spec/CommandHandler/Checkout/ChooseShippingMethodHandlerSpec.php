<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;

final class ChooseShippingMethodHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $shippingMethodRepository,
            $shipmentRepository,
            $eligibilityChecker,
            $stateMachineFactory
        );
    }

    function it_assigns_choosen_shipping_method_to_specified_shipment(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        StateMachineInterface $stateMachine
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');
        $chooseShippingMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn($shipment);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $stateMachine->apply('select_shipping')->shouldBeCalled();

        $this($chooseShippingMethod)->shouldReturn($cart);
    }

    function it_throws_an_exception_if_shipping_method_is_not_eligible(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment,
        StateMachineInterface $stateMachine
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');
        $chooseShippingMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn($shipment);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShipmentInterface $shipment
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');

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
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        StateMachineInterface $stateMachine,
        ShipmentInterface $shipment
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);
        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(false);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_shipping_method_with_given_code_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        StateMachineInterface $stateMachine,
        ShipmentInterface $shipment
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');
        $chooseShippingMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn(null);

        $shipment->setMethod(Argument::type(ShippingMethodInterface::class))->shouldNotBeCalled();
        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }

    function it_throws_an_exception_if_ordered_shipment_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        FactoryInterface $stateMachineFactory,
        OrderInterface $cart,
        ShippingMethodInterface $shippingMethod,
        StateMachineInterface $stateMachine
    ): void {
        $chooseShippingMethod = new ChooseShippingMethod('DHL_SHIPPING_METHOD');
        $chooseShippingMethod->setOrderTokenValue('ORDERTOKEN');
        $chooseShippingMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($cart);

        $stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('select_shipping')->willReturn(true);

        $shippingMethodRepository->findOneBy(['code' => 'DHL_SHIPPING_METHOD'])->willReturn($shippingMethod);

        $cart->getId()->willReturn('111');

        $shipmentRepository->findOneByOrderId('123', '111')->willReturn(null);

        $stateMachine->apply('select_shipping')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$chooseShippingMethod])
        ;
    }
}
