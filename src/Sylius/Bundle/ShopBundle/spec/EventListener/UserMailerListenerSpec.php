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
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\ShopBundle\EventListener\UserMailerListener;
use Sylius\Bundle\UserBundle\EventListener\MailerListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UserMailerListenerSpec extends ObjectBehavior
{
    function let(SenderInterface $emailSender, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($emailSender, $channelContext);
    }

    function it_is_instializable()
    {
        $this->shouldHaveType(UserMailerListener::class);
    }

    function it_is_a_user_mailer_listener()
    {
        $this->shouldHaveType(MailerListener::class);
    }

    function it_throws_an_exception_if_event_subject_is_not_a_customer_instance_sending_confirmation(
        GenericEvent $event,
        \stdClass $customer
    ) {
        $event->getSubject()->willReturn($customer);

        $this->shouldThrow(\InvalidArgumentException::class)->during('sendUserRegistrationEmail', [$event]);
    }

    function it_does_not_send_the_email_confirmation_if_the_customer_user_is_null(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer
    ) {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_does_not_send_the_email_registration_if_the_customer_user_does_not_have_email(
        SenderInterface $emailSender,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user
    ) {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn(null);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->sendUserRegistrationEmail($event);
    }

    function it_sends_an_email_registration_successfully(
        SenderInterface $emailSender,
        ChannelContextInterface $channelContext,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel
    ) {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $customer->getEmail()->willReturn('fulanito@sylius.com');

        $user->getEmail()->willReturn('fulanito@sylius.com');

        $channelContext->getChannel()->willReturn($channel);

        $emailSender
            ->send(
                Emails::USER_REGISTRATION,
                [
                    'fulanito@sylius.com',
                ],
                [
                    'user' => $user,
                ]
            )
            ->shouldBeCalled()
        ;

        $this->sendUserRegistrationEmail($event);
    }
}
