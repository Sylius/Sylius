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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerDefaultAddressListenerSpec extends ObjectBehavior
{
    function it_adds_the_address_as_default_to_the_customer_on_pre_create_resource_controller_event(
        ResourceControllerEvent $event,
        AddressInterface $address,
        CustomerInterface $customer,
    ): void {
        $event->getSubject()->willReturn($address);
        $address->getCustomer()->willReturn($customer);

        $address->getId()->shouldBeCalled();
        $address->getCustomer()->shouldBeCalled();

        $customer->getDefaultAddress()->willReturn(null);
        $customer->getDefaultAddress()->shouldBeCalled();

        $customer->setDefaultAddress($address)->shouldBeCalled();

        $this->preCreate($event);
    }

    function it_does_not_set_address_as_default_if_customer_already_have_a_default_address(
        ResourceControllerEvent $event,
        AddressInterface $address,
        CustomerInterface $customer,
        AddressInterface $anotherAddress,
    ): void {
        $event->getSubject()->willReturn($address);
        $address->getCustomer()->willReturn($customer);

        $address->getId()->shouldBeCalled();
        $address->getCustomer()->shouldBeCalled();

        $customer->getDefaultAddress()->willReturn($anotherAddress);
        $customer->setDefaultAddress(Argument::any())->shouldNotBeCalled();

        $this->preCreate($event);
    }

    function it_throws_an_exception_if_event_subject_is_not_an_address(
        ResourceControllerEvent $event,
    ): void {
        $event->getSubject()->willReturn(Argument::any());

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preCreate', [$event])
        ;
    }

    function it_does_nothing_if_address_does_have_an_id(
        ResourceControllerEvent $event,
        AddressInterface $address,
    ): void {
        $event->getSubject()->willReturn($address);
        $address->getId()->willReturn(1);

        $address->getId()->shouldBeCalled();
        $address->getCustomer()->shouldNotBeCalled();

        $this->preCreate($event);
    }

    function it_does_nothing_if_address_does_not_have_a_customer_assigned(
        ResourceControllerEvent $event,
        AddressInterface $address,
        CustomerInterface $customer,
    ): void {
        $event->getSubject()->willReturn($address);
        $address->getCustomer()->willReturn(null);

        $address->getId()->shouldBeCalled();
        $customer->getDefaultAddress()->shouldNotBeCalled();

        $this->preCreate($event);
    }
}
