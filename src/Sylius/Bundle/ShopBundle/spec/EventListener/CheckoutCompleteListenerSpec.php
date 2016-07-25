<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ShopBundle\EventListener\CheckoutCompleteListener;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @mixin CheckoutCompleteListener
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutCompleteListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShopBundle\EventListener\CheckoutCompleteListener');
    }

    function it_sets_order_id_in_session(SessionInterface $session, OrderInterface $order, GenericEvent $event)
    {
        $event->getSubject()->willReturn($order);
        $order->getId()->willReturn(1);
        $session->set('sylius_order_id', 1)->shouldBeCalled();

        $this->onCheckoutComplete($event);
    }

    function it_throws_invalid_argument_exception_if_subject_is_not_an_order(
        SessionInterface $session,
        CountryInterface $country,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn($country);
        $session->set('sylius_order_id', Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('onCheckoutComplete', [$event]);
    }
}
