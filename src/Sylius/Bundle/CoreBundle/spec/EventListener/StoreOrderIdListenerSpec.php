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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\StoreOrderIdListener;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin StoreOrderIdListener
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class StoreOrderIdListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\StoreOrderIdListener');
    }

    function it_sets_order_id_in_session(SessionInterface $session, OrderInterface $order, GenericEvent $event)
    {
        $event->getSubject()->willReturn($order);
        $order->getId()->willReturn(1);
        $session->set('sylius_order_id', 1)->shouldBeCalled();

        $this->setOrderId($event);
    }

    function it_throws_invalid_argument_exception_if_subject_is_not_an_order(
        SessionInterface $session,
        CountryInterface $country,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($country);
        $session->set('sylius_order_id', Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('setOrderId', [$event]);
    }
}
