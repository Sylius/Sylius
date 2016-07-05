<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\StateMachineCallback;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SetGuestCustomerCallbackSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\SetGuestCustomerCallback');
    }

    function it_sets_guest_customer_id_in_session(
        SessionInterface $session,
        OrderInterface $order,
        CustomerInterface $customer
    ) {
        $order->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $session->set('sylius_customer_guest_id', 1)->shouldBeCalled();

        $this->setCustomerId($order);
    }
}
