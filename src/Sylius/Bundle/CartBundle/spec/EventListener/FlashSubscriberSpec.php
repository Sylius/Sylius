<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Cart\Event\CartEvents;
use Sylius\Component\Cart\Event\CartItemEvents;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlashSubscriberSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashSubscriber');
    }

    function it_should_add_a_custom_error_flash_message_from_event($session, ResourceEvent $event, FlashBag $flashBag)
    {
        $event
            ->getMessage()
            ->willReturn('Custom message')
        ;

        $event
            ->getName()
            ->willReturn(CartEvents::POST_CLEAR)
        ;

        $session
            ->getBag('flashes')
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('error', 'Custom message')
            ->willReturn(null)
        ;

        $this->addErrorFlash($event);
    }

    function it_should_add_a_custom_success_flash_message_from_event($session, ResourceEvent $event, FlashBag $flashBag)
    {
        $event
            ->getMessage()
            ->willReturn('Custom success message')
        ;

        $event
            ->getName()
            ->willReturn(CartEvents::POST_CLEAR)
        ;

        $session
            ->getBag(Argument::exact('flashes'))
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('success', 'Custom success message')
            ->willReturn(null)
        ;

        $this->addSuccessFlash($event);
    }

    function it_should_have_a_default_error_flash_message_for_event_name(
        $session,
        ResourceEvent $event,
        FlashBag $flashBag
    ) {
        $event
            ->getMessage()
            ->willReturn(null)
        ;

        $event
            ->getName()
            ->willReturn(CartItemEvents::ADD_FAILED)
        ;

        $session
            ->getBag('flashes')
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('error', 'sylius.cart_item.add_failed')
            ->shouldBeCalled();
        ;

        $this->addErrorFlash($event);
    }

    function it_should_have_a_default_success_flash_message_for_event_name(
        $session,
        ResourceEvent $event,
        FlashBag $flashBag
    ) {
        $event
            ->getMessage()
            ->willReturn(null)
        ;

        $event
            ->getName()
            ->willReturn(CartEvents::POST_SAVE)
        ;

        $session
            ->getBag('flashes')
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('success', 'sylius.cart.save')
            ->willReturn(null)
        ;

        $this->addSuccessFlash($event);
    }
}
