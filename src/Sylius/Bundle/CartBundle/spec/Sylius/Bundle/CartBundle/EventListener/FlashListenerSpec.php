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
use Sylius\Bundle\CartBundle\Event\FlashEvent;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->beConstructedWith($session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashListener');
    }

    function it_should_add_a_custom_error_flash_message_from_event($session, FlashEvent $event, FlashBag $flashBag)
    {
        $message = "This is an error message";

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn($message)
        ;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_ERROR)
        ;

        $session
            ->getBag(Argument::exact('flashes'))
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('error', $message)
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->addErrorFlash($event)
        ;
    }

    function it_should_add_a_custom_success_flash_message_from_event($session, FlashEvent $event, FlashBag $flashBag)
    {
        $message = "This is an success message";

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn($message)
        ;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_COMPLETED)
        ;

        $session
            ->getBag(Argument::exact('flashes'))
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $flashBag
            ->add('success', $message)
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->addSuccessFlash($event)
        ;
    }

    function it_should_have_a_default_error_flash_message_for_event_name($session, $translator, FlashEvent $event, FlashBag $flashBag)
    {
        $messages = array(SyliusCartEvents::ITEM_ADD_ERROR => 'Error occurred while adding item to cart.');
        $this->messages = $messages;

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_ERROR)
        ;

        $session
            ->getBag(Argument::exact('flashes'))
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $translator
            ->trans(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($messages[SyliusCartEvents::ITEM_ADD_ERROR])
        ;

        $flashBag
            ->add('error', $messages[SyliusCartEvents::ITEM_ADD_ERROR])
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->addErrorFlash($event)
        ;
    }

    function it_should_have_a_default_success_flash_message_for_event_name($session, $translator, FlashEvent $event, FlashBag $flashBag)
    {
        $messages = array(SyliusCartEvents::ITEM_ADD_COMPLETED => 'The cart has been successfully updated.');
        $this->messages = $messages;

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_COMPLETED)
        ;

        $session
            ->getBag(Argument::exact('flashes'))
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $translator
            ->trans(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($messages[SyliusCartEvents::ITEM_ADD_COMPLETED])
        ;

        $flashBag
            ->add('success', $messages[SyliusCartEvents::ITEM_ADD_COMPLETED])
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->addSuccessFlash($event)
        ;
    }
}
