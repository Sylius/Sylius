<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\CheckoutAddressingListener;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Liverbool <nukboon@gmail.com>
 */
final class CheckoutAddressingListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutAddressingListener::class);
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringSetCustomerAddressing($event)
        ;
    }

    function it_does_nothing_when_context_doesnt_have_customer(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $this->setCustomerAddressing($event);
    }

    function it_sets_customer_default_address_from_order(GenericEvent $event, OrderInterface $order, CustomerInterface $customer, AddressInterface $address)
    {
        $event->getSubject()->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $order->getShippingAddress()->willReturn($address);
        $order->getBillingAddress()->willReturn($address);

        $customer->getDefaultAddress()->willReturn(null);
        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->setCustomerAddressing($event);
    }

    function it_does_not_set_customer_default_address_when_they_already_have_one(GenericEvent $event, OrderInterface $order, CustomerInterface $customer, AddressInterface $address)
    {
        $event->getSubject()->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $order->getShippingAddress()->willReturn($address);
        $order->getBillingAddress()->willReturn($address);

        $customer->getDefaultAddress()->willReturn($address);
        $customer->setDefaultAddress($address)->shouldNotBeCalled();

        $this->setCustomerAddressing($event);
    }
}
