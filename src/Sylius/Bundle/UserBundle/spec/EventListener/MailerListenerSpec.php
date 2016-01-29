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
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MailerListenerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender)
    {
        $this->beConstructedWith($sender);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\MailerListener');
    }

    function it_send_password_reset_token_mail($sender, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $sender->send('reset_password_token', ['test@example.com'], Argument::any())->shouldBeCalled();

        $this->sendResetPasswordTokenEmail($event);
    }

    function it_send_password_reset_pin_mail($sender, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $sender->send('reset_password_pin', ['test@example.com'], Argument::any())->shouldBeCalled();

        $this->sendResetPasswordPinEmail($event);
    }
}
