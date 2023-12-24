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
use Sylius\Component\Core\Model\ChannelInterface;
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
        ChannelInterface $channel,
    ): void {
        $event->getSubject()->willReturn($customer);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendUserRegistrationEmail', [$event]);
    }

    function it_does_not_send_the_email_confirmation_if_the_customer_user_is_null(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer,
        ChannelInterface $channel,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $channel->isAccountVerificationRequired()->willReturn(false);

        $emailSender->send(Argument::cetera())->shouldNotBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_does_not_send_the_email_registration_if_the_customer_user_does_not_have_email(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn(null);

        $channel->isAccountVerificationRequired()->willReturn(false);

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

        $channel->isAccountVerificationRequired()->willReturn(false);

        $emailSender->send(Emails::USER_REGISTRATION, ['fulanito@sylius.com'], [
            'user' => $user,
            'channel' => $channel,
            'localeCode' => 'en_US',
        ])->shouldBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_does_nothing_when_account_verification_is_required(
        ChannelInterface $channel,
        GenericEvent $event,
    ): void {
        $channel->isAccountVerificationRequired()->willReturn(true);

        $event->getSubject()->shouldNotBeCalled();

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

    function it_sends_verification_success_email(
        SenderInterface $emailSender,
        GenericEvent $event,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $event->getSubject()->willReturn($shopUser);
        $shopUser->getEmail()->willReturn('shop@example.com');

        $emailSender->send(
            Emails::USER_REGISTRATION,
            ['shop@example.com'],
            [
                'user' => $shopUser,
                'channel' => $channel,
                'localeCode' => 'en_US',
            ],
        )->shouldBeCalled();

        $this->sendVerificationSuccessEmail($event);
    }

    function it_throws_exception_while_sending_verification_success_email_when_event_subject_is_not_a_shop_user(
        GenericEvent $event,
    ): void {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendVerificationSuccessEmail', [$event]);
    }
}
