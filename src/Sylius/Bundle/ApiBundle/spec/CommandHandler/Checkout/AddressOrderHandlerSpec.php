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

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AddressOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory
    ): void {
        $this->beConstructedWith($orderRepository, $customerFactory, $manager, $stateMachineFactory);
    }

    function it_handles_order_shipment_addressing(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $customerFactory->createNew()->willReturn($customer);
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $customer->setEmail('r2d2@droid.com')->shouldBeCalled();
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();

        $manager->persist($customer)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);
        $stateMachine->apply('address')->shouldBeCalled();

        $this($addressOrder);
    }

    function it_throws_an_exception_if_order_does_not_exist(
        AddressInterface $billingAddress,
        OrderRepositoryInterface $orderRepository
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        AddressInterface $billingAddress,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }
}
