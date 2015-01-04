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
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Model\AddressInterface;

/**
 * Automatic set user's default addressing
*
 * @author Liverbool <nukboon@gmail.com>
 */
class CheckoutAddressingListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CheckoutAddressingListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetUserAddressing($event)
        ;
    }

    function it_does_nothing_when_context_doesnt_have_user(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->getUser()->willReturn(null);

        $this->setUserAddressing($event);
    }

    function it_sets_user_default_addressing_from_order(GenericEvent $event, OrderInterface $order, UserInterface $user, AddressInterface $address)
    {
        $event->getSubject()->willReturn($order);

        $order->getUser()->willReturn($user);

        $order->getShippingAddress()->willReturn($address);
        $user->getShippingAddress()->willReturn(null);
        $user->setShippingAddress($address)->shouldBeCalled();

        $order->getBillingAddress()->willReturn($address);
        $user->getBillingAddress()->willReturn(null);
        $user->setBillingAddress($address)->shouldBeCalled();

        $this->setUserAddressing($event);
    }

    function it_does_not_sets_user_addressing_when_user_already_have_default_addresses(GenericEvent $event, OrderInterface $order, UserInterface $user, AddressInterface $address)
    {
        $event->getSubject()->willReturn($order);

        $order->getUser()->willReturn($user);

        $order->getShippingAddress()->willReturn($address);
        $user->getShippingAddress()->willReturn($address);
        $user->setShippingAddress($address)->shouldNotBeCalled();

        $order->getBillingAddress()->willReturn($address);
        $user->getBillingAddress()->willReturn($address);
        $user->setBillingAddress($address)->shouldNotBeCalled();

        $this->setUserAddressing($event);
    }
}
