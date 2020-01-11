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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UserRegistrationListenerSpec extends ObjectBehavior
{
    function let(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
        UserLoginInterface $userLogin
    ): void {
        $this->beConstructedWith(
            $userManager,
            $tokenGenerator,
            $eventDispatcher,
            $channelContext,
            $userLogin,
            'shop'
        );
    }

    function it_sends_an_user_verification_email(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(true);

        $tokenGenerator->generate()->willReturn('1d7dbc5c3dbebe5c');
        $user->setEmailVerificationToken('1d7dbc5c3dbebe5c')->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $eventDispatcher
            ->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, Argument::type(GenericEvent::class))
            ->shouldBeCalled()
        ;

        $this->handleUserVerification($event);
    }

    function it_enables_and_signs_in_user(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
        UserLoginInterface $userLogin,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);

        $user->setEnabled(true)->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $userLogin->login($user, 'shop')->shouldBeCalled();

        $tokenGenerator->generate()->shouldNotBeCalled();
        $user->setEmailVerificationToken(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, Argument::type(GenericEvent::class))
            ->shouldNotBeCalled()
        ;

        $this->handleUserVerification($event);
    }

    function it_does_not_send_verification_email_if_it_is_not_required_on_channel(
        ObjectManager $userManager,
        GeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher,
        ChannelContextInterface $channelContext,
        UserLoginInterface $userLogin,
        GenericEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user,
        ChannelInterface $channel
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $channelContext->getChannel()->willReturn($channel);
        $channel->isAccountVerificationRequired()->willReturn(false);

        $user->setEnabled(true)->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $userLogin->login($user, 'shop')->shouldBeCalled();

        $tokenGenerator->generate()->shouldNotBeCalled();
        $user->setEmailVerificationToken(Argument::any())->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, Argument::type(GenericEvent::class))
            ->shouldNotBeCalled()
        ;

        $this->handleUserVerification($event);
    }

    function it_throws_an_invalid_argument_exception_if_event_subject_is_not_customer_type(
        GenericEvent $event,
        \stdClass $customer
    ): void {
        $event->getSubject()->willReturn($customer);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [$event]);
    }

    function it_throws_an_invalid_argument_exception_if_user_is_null(
        GenericEvent $event,
        CustomerInterface $customer
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('handleUserVerification', [$event]);
    }
}
