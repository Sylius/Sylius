<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class MailerListenerSpec extends ObjectBehavior
{
    function let(
        SenderInterface $emailSender,
        ChannelContextInterface $channelContext,
        LocaleContextInterface $localeContext,
        ChannelInterface $channel,
    ): void {
        $this->beConstructedWith($emailSender, $channelContext, $localeContext);

        $channelContext->getChannel()->willReturn($channel);
        $localeContext->getLocaleCode()->willReturn('en_US');
    }

    function it_throws_an_exception_if_event_subject_is_not_a_customer_instance_sending_confirmation(
        GenericEvent $event,
        \stdClass $customer,
    ): void {
        $event->getSubject()->willReturn($customer);

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendUserRegistrationEmail', [$event]);
    }

    function it_does_not_send_the_email_confirmation_if_the_customer_user_is_null(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $emailSender->send(Argument::cetera())->shouldNotBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_does_not_send_the_email_registration_if_the_customer_user_does_not_have_email(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn(null);

        $emailSender->send(Argument::cetera())->shouldNotBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_sends_an_email_registration_successfully(
        SenderInterface $emailSender,
        ChannelInterface $channel,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn('fulanito@sylius.com');

        $user->getEmail()->willReturn('fulanito@sylius.com');

        $emailSender->send(Emails::USER_REGISTRATION, ['fulanito@sylius.com'], [
            'user' => $user,
            'channel' => $channel,
            'localeCode' => 'en_US',
        ])->shouldBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_send_password_reset_token_mail(
        SenderInterface $emailSender,
        ChannelInterface $channel,
        GenericEvent $event,
        UserInterface $user,
    ): void {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $emailSender->send('reset_password_token', ['test@example.com'], [
            'user' => $user,
            'channel' => $channel,
            'localeCode' => 'en_US',
        ])->shouldBeCalled();

        $this->sendResetPasswordTokenEmail($event);
    }

    function it_send_password_reset_pin_mail(
        SenderInterface $emailSender,
        ChannelInterface $channel,
        GenericEvent $event,
        UserInterface $user,
    ): void {
        $event->getSubject()->willReturn($user);

        $user->getEmail()->willReturn('test@example.com');

        $emailSender->send('reset_password_pin', ['test@example.com'], [
            'user' => $user,
            'channel' => $channel,
            'localeCode' => 'en_US',
        ])->shouldBeCalled();

        $this->sendResetPasswordPinEmail($event);
    }
}
