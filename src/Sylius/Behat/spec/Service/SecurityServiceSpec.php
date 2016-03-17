<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        SessionInterface $session,
        CookieSetterInterface $cookieSetter
    ) {
        $this->beConstructedWith($userRepository, $session, $cookieSetter, 'context_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Service\SecurityService');
    }

    function it_implements_security_service_interface()
    {
        $this->shouldImplement(SecurityServiceInterface::class);
    }

    function it_logs_user_in(
        UserRepositoryInterface $userRepository,
        SessionInterface $session,
        CookieSetterInterface $cookieSetter,
        UserInterface $user
    ) {
        $userRepository->findOneBy(['username' => 'sylius@example.com'])->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_USER']);
        $user->getPassword()->willReturn('xyz');
        $user->serialize()->willReturn('serialized_user');

        $session->set('_security_context_name', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();

        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn('sylius@example.com');
    }

    function it_does_not_log_user_in_if_user_was_not_found(
        UserRepositoryInterface $userRepository,
        SessionInterface $session,
        CookieSetterInterface $cookieSetter
    ) {
        $userRepository->findOneBy(['username' => 'sylius@example.com'])->willReturn(null);

        $session->set(Argument::cetera())->shouldNotBeCalled();
        $session->save()->shouldNotBeCalled();

        $cookieSetter->setCookie(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(new \InvalidArgumentException(sprintf('There is no user with email sylius@example.com')))
            ->during('logIn', ['sylius@example.com'])
        ;
    }
}
