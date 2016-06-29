<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\CoreBundle\EventListener\CustomerAwareListener;
use Sylius\UserBundle\EventListener\CustomerAwareListener as BaseCustomerAwareListener;
use Sylius\Cart\Event\CartEvent;
use Sylius\User\Context\CustomerContextInterface;
use Sylius\User\Model\CustomerAwareInterface;
use Sylius\User\Model\CustomerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerAwareListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerAwareListener::class);
    }

    function it_extends_user_customer_aware_interface()
    {
        $this->shouldHaveType(BaseCustomerAwareListener::class);
    }

    function let(CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerContext);
    }

    function it_sets_customer_on_a_cart(
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
