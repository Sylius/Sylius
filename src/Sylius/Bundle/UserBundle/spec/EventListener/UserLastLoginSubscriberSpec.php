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

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class UserLastLoginSubscriberSpec extends ObjectBehavior
{
    function let(ObjectManager $userManager): void
    {
        $this->beConstructedWith($userManager, 'Sylius\Component\User\Model\UserInterface', null);
    }

    function it_is_subscriber(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function its_subscribed_to_events(): void
    {
        $this::getSubscribedEvents()->shouldReturn([
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
        ]);
    }

    function it_updates_user_last_login_on_security_interactive_login(
        ObjectManager $userManager,
        Request $request,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $token->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_updates_user_last_login_on_implicit_login(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user,
    ): void {
        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onImplicitLogin($event);
    }

    function it_updates_only_sylius_user_specified_in_constructor(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, 'FakeBundle\User\Model\User', null);

        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();
        $userManager->persist(Argument::any())->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this->onImplicitLogin($event);
    }

    function it_updates_only_user_specified_in_constructor(
        ObjectManager $userManager,
        UserEvent $event,
        Request $request,
        TokenInterface $token,
        SymfonyUserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, 'FakeBundle\User\Model\User', null);

        $token->getUser()->willReturn($user);

        $event->getUser()->willReturn($user);

        $userManager->persist(Argument::any())->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_throws_exception_if_subscriber_is_used_for_class_other_than_sylius_user_interface(
        ObjectManager $userManager,
        Request $request,
        TokenInterface $token,
        SymfonyUserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, SymfonyUserInterface::class, null);

        $token->getUser()->willReturn($user);

        $userManager->persist(Argument::any())->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this
            ->shouldThrow(\UnexpectedValueException::class)
            ->during('onSecurityInteractiveLogin', [new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject())])
        ;
    }

    function it_sets_last_login_when_there_was_none_and_interval_is_present_on_interactive_login(
        ObjectManager $userManager,
        Request $request,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $token->getUser()->willReturn($user);

        $user->getLastLogin()->willReturn(null);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_sets_last_login_when_there_was_none_and_interval_is_present_on_implicit_login(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $user->getLastLogin()->willReturn(null);

        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onImplicitLogin($event);
    }

    function it_does_nothing_when_tracking_interval_is_set_and_user_was_updated_within_it_on_interactive_login(
        ObjectManager $userManager,
        Request $request,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $token->getUser()->willReturn($user);

        $lastLogin = (new \DateTime())->modify('-6 hours');
        $user->getLastLogin()->willReturn($lastLogin);

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();

        $userManager->persist($user)->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_when_tracking_interval_is_set_and_user_was_updated_within_it_on_implicit_login(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $lastLogin = (new \DateTime())->modify('-6 hours');
        $user->getLastLogin()->willReturn($lastLogin);

        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();

        $userManager->persist($user)->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this->onImplicitLogin($event);
    }

    function it_updates_last_login_when_the_previous_is_older_than_the_interval_on_interactive_login(
        ObjectManager $userManager,
        Request $request,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $token->getUser()->willReturn($user);

        $lastLogin = (new \DateTime())->modify('-3 days');
        $user->getLastLogin()->willReturn($lastLogin);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_updates_last_login_when_the_previous_is_older_than_the_interval_on_implicit_login(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user,
    ): void {
        $this->beConstructedWith($userManager, UserInterface::class, 'P1D');

        $lastLogin = (new \DateTime())->modify('-3 days');
        $user->getLastLogin()->willReturn($lastLogin);

        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTimeInterface::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onImplicitLogin($event);
    }
}
