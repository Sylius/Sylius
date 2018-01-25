<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailerListenerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_send_password_reset_token_mail(SenderInterface $sender, GenericEvent $event, UserInterface $user): void
    {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $sender->send('reset_password_token', ['test@example.com'], Argument::any())->shouldBeCalled();

        $this->sendResetPasswordTokenEmail($event);
    }

    function it_send_password_reset_pin_mail(SenderInterface $sender, GenericEvent $event, UserInterface $user): void
    {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $sender->send('reset_password_pin', ['test@example.com'], Argument::any())->shouldBeCalled();

        $this->sendResetPasswordPinEmail($event);
    }
}
