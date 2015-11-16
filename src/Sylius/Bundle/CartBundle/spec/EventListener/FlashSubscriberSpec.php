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
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartEvents;
use Sylius\Component\Cart\Event\CartItemEvents;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlashSubscriberSpec extends ObjectBehavior
{
    function let(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->beConstructedWith($session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashSubscriber');
    }

    function it_should_add_a_custom_error_flash_message_from_event($session, $translator, CartEvent $event, FlashBag $flashBag)
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
            ->shouldBeCalled()
        ;

        $translator
            ->trans('Custom message', array(), 'flashes')
            ->willReturn('Custom message');
        ;

        $this->addErrorFlash($event);
    }

    function it_should_add_a_custom_success_flash_message_from_event($session, $translator, CartEvent $event, FlashBag $flashBag)
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
            ->getBag('flashes')
            ->willReturn($flashBag)
        ;

        $translator
            ->trans('Custom success message', array(), 'flashes')
            ->willReturn('Custom success message')
        ;


        $flashBag
            ->add('success', 'Custom success message')
            ->willReturn(null)
        ;

        $this->addSuccessFlash($event);
    }

    function it_should_have_a_default_error_flash_message_for_event_name(
        $session,
        $translator,
        CartEvent $event,
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

        $translator
            ->trans('sylius.cart_item.add_failed', array(), 'flashes')
            ->willReturn('Item could not be added to the cart.')
        ;

        $flashBag
            ->add('error', 'Item could not be added to the cart.')
            ->shouldBeCalled();
        ;

        $this->addErrorFlash($event);
    }

    function it_should_have_a_default_success_flash_message_for_event_name(
        $session,
        $translator,
        CartEvent $event,
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

        $translator
            ->trans('sylius.cart.save', array(), 'flashes')
            ->willReturn('Cart has been successfully saved.')
        ;

        $flashBag
            ->add('success', 'Cart has been successfully saved.')
            ->shouldBeCalled()
        ;

        $this->addSuccessFlash($event);
    }
}
