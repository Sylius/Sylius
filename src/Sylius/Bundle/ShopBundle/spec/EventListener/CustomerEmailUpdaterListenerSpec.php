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

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CustomerEmailUpdaterListenerSpec extends ObjectBehavior
{
    function let(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        SectionProviderInterface $sectionResolver,
    ): void {
        $this->beConstructedWith($tokenGenerator, $channelContext, $eventDispatcher, $session, $sectionResolver);
    }

    function it_does_nothing_change_was_performed_by_admin(
        SectionProviderInterface $sectionResolver,
        SectionInterface $section,
        GenericEvent $event,
    ): void {
        $sectionResolver->getSection()->willReturn($section);

        $event->getSubject()->shouldNotBeCalled();

        $this->eraseVerification($event);
        $this->sendVerificationEmail($event);
    }

    function it_removes_user_verification_and_disables_user_if_email_has_been_changed_and_channel_requires_verification(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $customer->getEmail()->willReturn('new@example.com');
        $user->getUsername()->willReturn('old@example.com');

        $tokenGenerator->generate()->willReturn('1d7dbc5c3dbebe5c');

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $user->setVerifiedAt(null)->shouldBeCalled();
        $user->setEmailVerificationToken('1d7dbc5c3dbebe5c')->shouldBeCalled();
        $user->setEnabled(false)->shouldBeCalled();

        $this->eraseVerification($event);
    }

    function it_removes_user_verification_only_if_email_has_been_changed_but_channel_does_not_require_verification(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $customer->getEmail()->willReturn('new@example.com');
        $user->getUsername()->willReturn('old@example.com');

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);

        $user->setVerifiedAt(null)->shouldBeCalled();

        $tokenGenerator->generate()->shouldNotBeCalled();
        $user->setEmailVerificationToken(Argument::any())->shouldNotBeCalled();
        $user->setEnabled(false)->shouldNotBeCalled();

        $this->eraseVerification($event);
    }

    function it_does_nothing_if_email_has_not_been_changed(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $customer->getEmail()->willReturn('new@example.com');
        $user->getUsername()->willReturn('new@example.com');

        $channelContext->getChannel()->shouldNotBeCalled();

        $tokenGenerator->generate()->shouldNotBeCalled();

        $user->setVerifiedAt(null)->shouldNotBeCalled();
        $user->setEmailVerificationToken(Argument::any())->shouldNotBeCalled();
        $user->setEnabled(false)->shouldNotBeCalled();

        $this->eraseVerification($event);
    }

    function it_throws_an_invalid_argument_exception_if_event_subject_is_not_customer_type(
        GenericEvent $event,
        \stdClass $customer,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $event->getSubject()->willReturn($customer);

        $this->shouldThrow(\InvalidArgumentException::class)->during('eraseVerification', [$event]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('sendVerificationEmail', [$event]);
    }

    function it_throws_an_invalid_argument_exception_if_user_is_null(
        GenericEvent $event,
        CustomerInterface $customer,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('eraseVerification', [$event]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('sendVerificationEmail', [$event]);
    }

    function it_sends_verification_email_and_adds_flash_if_user_verification_is_required(
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        FlashBagInterface $flashBag,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $user->isEnabled()->willReturn(false);
        $user->isVerified()->willReturn(false);
        $user->getEmailVerificationToken()->willReturn('1d7dbc5c3dbebe5c');

        $session->getBag('flashes')->willReturn($flashBag);
        $flashBag->add('success', 'sylius.user.verify_email_request')->shouldBeCalled();

        $eventDispatcher
            ->dispatch(Argument::type(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
            ->shouldBeCalled()
        ;

        $this->sendVerificationEmail($event);
    }

    function it_does_not_send_email_if_user_is_still_enabled(
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        FlashBagInterface $flashBag,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $user->isEnabled()->willReturn(true);
        $user->isVerified()->willReturn(false);
        $user->getEmailVerificationToken()->willReturn('1d7dbc5c3dbebe5c');

        $session->getBag('flashes')->shouldNotBeCalled();
        $flashBag->add(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(Argument::type(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
            ->shouldNotBeCalled()
        ;

        $this->sendVerificationEmail($event);
    }

    function it_does_not_send_email_if_user_is_still_verified(
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        FlashBagInterface $flashBag,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $user->isEnabled()->willReturn(false);
        $user->isVerified()->willReturn(true);
        $user->getEmailVerificationToken()->willReturn('1d7dbc5c3dbebe5c');

        $session->getBag('flashes')->shouldNotBeCalled();
        $flashBag->add(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(Argument::type(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
            ->shouldNotBeCalled()
        ;

        $this->sendVerificationEmail($event);
    }

    function it_does_not_send_email_if_user_does_not_have_verification_token(
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        FlashBagInterface $flashBag,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $user->isEnabled()->willReturn(false);
        $user->isVerified()->willReturn(false);
        $user->getEmailVerificationToken()->willReturn(null);

        $session->getBag('flashes')->shouldNotBeCalled();
        $flashBag->add(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(Argument::type(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
            ->shouldNotBeCalled()
        ;

        $this->sendVerificationEmail($event);
    }

    function it_does_nothing_if_channel_does_not_require_verification(
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        FlashBagInterface $flashBag,
        ChannelInterface $channel,
        SectionProviderInterface $sectionResolver,
        ShopSection $shopSection,
    ): void {
        $sectionResolver->getSection()->willReturn($shopSection);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);

        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $session->getBag('flashes')->shouldNotBeCalled();
        $flashBag->add(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(Argument::type(GenericEvent::class), UserEvents::REQUEST_VERIFICATION_TOKEN)
            ->shouldNotBeCalled()
        ;

        $this->sendVerificationEmail($event);
        $this->sendVerificationEmail($event);
        $this->sendVerificationEmail($event);
    }
}
