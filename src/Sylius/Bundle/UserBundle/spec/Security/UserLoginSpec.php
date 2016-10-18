<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\Security\UserLogin;
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class UserLoginSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($tokenStorage, $userChecker, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserLogin::class);
    }

    function it_implements_user_login_interface()
    {
        $this->shouldImplement(UserLoginInterface::class);
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_is_disabled(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        EventDispatcherInterface $eventDispatcher,
        UserInterface $user
    ) {
        $user->getRoles()->willReturn(['ROLE_TEST']);
        $userChecker->checkPreAuth($user)->willThrow(DisabledException::class);

        $tokenStorage->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(DisabledException::class)->during('login', [$user]);
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_account_status_is_invalid(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        EventDispatcherInterface $eventDispatcher,
        UserInterface $user
    ) {
        $user->getRoles()->willReturn(['ROLE_TEST']);
        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->willThrow(CredentialsExpiredException::class);

        $tokenStorage->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(CredentialsExpiredException::class)->during('login', [$user]);
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_has_no_roles(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        EventDispatcherInterface $eventDispatcher,
        UserInterface $user
    ) {
        $user->getRoles()->willReturn([]);
        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->shouldBeCalled();

        $tokenStorage->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(AuthenticationException::class)->during('login', [$user]);
    }

    function it_logs_user_in(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        EventDispatcherInterface $eventDispatcher,
        UserInterface $user
    ) {
        $user->getRoles()->willReturn(['ROLE_TEST']);

        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->shouldBeCalled();
        $tokenStorage->setToken(Argument::type(UsernamePasswordToken::class))->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldBeCalled();

        $this->login($user);
    }
}
