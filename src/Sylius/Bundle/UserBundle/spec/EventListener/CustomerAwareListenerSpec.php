<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerAwareListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\CustomerAwareListener');
    }

    public function let(CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerContext);
    }

    public function it_throws_exception_when_object_is_not_customer(GenericEvent $event, \stdClass $object)
    {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetCustomer($event)
        ;
    }

    public function it_does_nothing_when_context_doesnt_have_customer(
        $customerContext,
        GenericEvent $event,
        CustomerAwareInterface $resource
    ) {
        $event->getSubject()->willReturn($resource);
        $customerContext->getCustomer()->willReturn(null);

        $resource->setCustomer(Argument::any())->shouldNotBeCalled();

        $this->setCustomer($event);
    }

    public function it_sets_customer_on_a_resource(
        $customerContext,
        GenericEvent $event,
        CustomerAwareInterface $resource,
        CustomerInterface $customer
    ) {
        $event->getSubject()->willReturn($resource);
        $customerContext->getCustomer()->willReturn($customer);

        $resource->setCustomer($customer)->shouldBeCalled();

        $this->setCustomer($event);
    }

    public function it_sets_customer_on_a_cart(
        $customerContext,
        CartEvent $event,
        CustomerAwareInterface $cart,
        CustomerInterface $customer
    ) {
        $event->getCart()->willReturn($cart);
        $customerContext->getCustomer()->willReturn($customer);

        $cart->setCustomer($customer)->shouldBeCalled();

        $this->setCustomer($event);
    }
}
