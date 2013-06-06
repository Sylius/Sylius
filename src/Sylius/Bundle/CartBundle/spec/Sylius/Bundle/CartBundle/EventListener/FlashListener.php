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

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\CartBundle\SyliusCartEvents;

/**
 * Flash message listener spec.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashListener extends ObjectBehavior
{

    /**
     * @param Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param Symfony\Component\Translation\TranslatorInterface $translator
     */
    function let($session, $translator)
    {
        $this->beConstructedWith($session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\FlashListener');
    }

    /**
     * @param Symfony\Component\EventDispatcher\Event $event
     * @param Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag
     */
    function it_should_add_a_custom_error_flash_message_from_event($session, $event, $flashBag)
    {

        $message = "This is an error message";

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn($message)
        ;

        $session
            ->getFlashBag()
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

    /**
     * @param Symfony\Component\EventDispatcher\Event $event
     * @param Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag
     */
    function it_should_add_a_custom_success_flash_message_from_event($session, $event, $flashBag)
    {

        $message = "This is an success message";

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn($message)
        ;

        $session
            ->getFlashBag()
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

    /**
     * @param Symfony\Component\EventDispatcher\Event $event
     * @param Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag
     */
    function it_should_have_a_default_error_flash_message_for_event_name($session, $translator, $event, $flashBag, $cartEvents)
    {
        $messages = array(SyliusCartEvents::ITEM_ADD_ERROR => 'Error occurred while adding item to cart.');
        $this->getWrappedSubject()->messages = $messages;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_ERROR)
        ;

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $session
            ->getFlashBag()
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $translator
            ->trans(ANY_ARGUMENTS)
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

    /**
     * @param Symfony\Component\EventDispatcher\Event $event
     * @param Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag
     */
    function it_should_have_a_default_success_flash_message_for_event_name($session, $translator, $event, $flashBag, $cartEvents)
    {
        $messages = array(SyliusCartEvents::ITEM_ADD_COMPLETED => 'The cart have been updated correctly.');
        $this->getWrappedSubject()->messages = $messages;

        $event
            ->getName()
            ->shouldBeCalled()
            ->willReturn(SyliusCartEvents::ITEM_ADD_COMPLETED)
        ;

        $event
            ->getMessage()
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $session
            ->getFlashBag()
            ->shouldBeCalled()
            ->willReturn($flashBag)
        ;

        $translator
            ->trans(ANY_ARGUMENTS)
            ->shouldBeCalled()
            ->willReturn($messages[SyliusCartEvents::ITEM_ADD_COMPLETED])
        ;

        $flashBag
            ->add('error', $messages[SyliusCartEvents::ITEM_ADD_COMPLETED])
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this
            ->addErrorFlash($event)
        ;
    }
}
