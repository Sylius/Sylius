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
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;
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
        $this->shouldImplement('Sylius\Bundle\UserBundle\Security\UserLoginInterface');
    }

    function it_throws_exception_and_does_not_log_user_in_when_user_check_will_throw_exception($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array('ROLE_TEST'));
        $userChecker->checkPreAuth($user)->willThrow('Symfony\Component\Security\Core\Exception\DisabledException');

        $securityContext->setToken(Argument::type('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'))->shouldNotBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type('Sylius\Bundle\UserBundle\Event\UserEvent'))->shouldNotBeCalled();

        $this->shouldThrow('Symfony\Component\Security\Core\Exception\DisabledException')->during('login', array($user));
    }

    function it_logs_user_in($securityContext, $userChecker, $eventDispatcher, UserInterface $user)
    {
        $user->getRoles()->willReturn(array('ROLE_TEST'));

        $userChecker->checkPreAuth($user)->shouldBeCalled();
        $securityContext->setToken(Argument::type('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'))->shouldBeCalled();
        $eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, Argument::type('Sylius\Bundle\UserBundle\Event\UserEvent'))->shouldBeCalled();

        $this->login($user);
    }
}
