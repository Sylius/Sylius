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
use Prophecy\Argument;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AddressOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressMapperInterface $addressMapper
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $customerRepository,
            $customerFactory,
            $manager,
            $stateMachineFactory,
            $addressMapper
        );
    }

    function it_handles_addressing_an_order_without_provided_shipping_address(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->setCustomer($customer);
        $order->getCustomer()->willReturn($customer);

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);

        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress(Argument::type(AddressInterface::class))->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $this($addressOrder);
    }

    function it_handles_addressing_an_order_for_visitor(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('r2d2@droid.com')->shouldBeCalled();
        $manager->persist($customer)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $manager->persist($order)->shouldBeCalled();

        $this($addressOrder);
    }

    function it_handles_addressing_an_order_for_logged_in_shop_user(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $customerFactory->createNew()->shouldNotBeCalled();
        $customer->setEmail('r2d2@droid.com')->shouldNotBeCalled();
        $manager->persist($customer)->shouldNotBeCalled();
        $order->setCustomer($customer)->shouldNotBeCalled();

        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $manager->persist($order)->shouldBeCalled();

        $this($addressOrder);
    }

    function it_handles_addressing_an_order_for_not_logged_in_shop_user(
        OrderRepositoryInterface $orderRepository,
        CustomerRepositoryInterface $customerRepository,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $customerRepository->findOneBy(['email' => 'r2d2@droid.com'])->willReturn($customer);

        $order->getCustomer()->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $order->getShippingAddress()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);
        $order->setBillingAddress($billingAddress)->shouldBeCalled();
        $order->setShippingAddress($shippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $manager->persist($order)->shouldBeCalled();

        $this($addressOrder);
    }

    function it_updates_order_address_based_on_data_form_new_order_address(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressMapperInterface $addressMapper,
        CustomerInterface $customer,
        AddressInterface $newBillingAddress,
        AddressInterface $newShippingAddress,
        AddressInterface $oldBillingAddress,
        AddressInterface $oldShippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $newBillingAddress->getWrappedObject(),
            $newShippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('r2d2@droid.com')->shouldBeCalled();
        $manager->persist($customer)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();
        $order->getBillingAddress()->willReturn($oldBillingAddress);
        $order->getShippingAddress()->willReturn($oldShippingAddress);

        $addressMapper->mapExisting($oldBillingAddress, $newBillingAddress)->willReturn($oldBillingAddress);
        $addressMapper->mapExisting($oldShippingAddress, $newShippingAddress)->willReturn($oldShippingAddress);

        $order->setBillingAddress($oldBillingAddress)->shouldBeCalled();
        $order->setShippingAddress($oldShippingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS)->shouldBeCalled();

        $manager->persist($order)->shouldBeCalled();

        $this($addressOrder);
    }

    function it_throws_an_exception_if_visitor_does_not_provide_an_email(
        OrderRepositoryInterface $orderRepository,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            null,
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(true);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(
            'r2d2@droid.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject()
        );
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }
}
