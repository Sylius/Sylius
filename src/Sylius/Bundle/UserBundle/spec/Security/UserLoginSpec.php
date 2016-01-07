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
use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserLoginSpec extends ObjectBehavior
{
    function let(SecurityContextInterface $securityContext, UserCheckerInterface $userChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($securityContext, $userChecker, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Security\UserLogin');
    }

    function it_implements_user_login_interface()
    {
        $this->shouldImplement(UserLoginInterface::class);
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_is_disabled($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array('ROLE_TEST'));
        $userChecker->checkPreAuth($user)->willThrow(DisabledException::class);

        $securityContext->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(DisabledException::class)->during('login', array($user));
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_account_status_is_invalid($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array('ROLE_TEST'));
        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->willThrow(CredentialsExpiredException::class);

        $securityContext->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(CredentialsExpiredException::class)->during('login', array($user));
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_has_no_roles($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array());
        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->shouldBeCalled();

        $securityContext->setToken(Argument::type(UsernamePasswordToken::class))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldNotBeCalled();

        $this->shouldThrow(AuthenticationException::class)->during('login', array($user));
    }

    function it_logs_user_in($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array('ROLE_TEST'));

        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $userChecker->checkPostAuth($user)->shouldBeCalled();
        $securityContext->setToken(Argument::type(UsernamePasswordToken::class))->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type(UserEvent::class))->shouldBeCalled();

        $this->login($user);
    }
}
