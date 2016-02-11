<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Test\Services;

use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Test\Factory\UserFactoryInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        UserFactoryInterface $userFactory,
        SessionInterface $session
    ) {
        $this->beConstructedWith($userRepository, $userFactory, $session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Test\Services\SecurityService');
    }

    function it_implements_security_service_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Test\Services\SecurityServiceInterface');
    }

    function it_logs_user_in(
        $userRepository,
        $session,
        UserInterface $user,
        Session $minkSession
    ) {
        $userRoles = ['ROLE_USER'];
        $userRepository->findOneBy(['username' => 'sylius@example.com'])->willReturn($user);
        $user->getRoles()->willReturn($userRoles);
        $user->getPassword()->willReturn('xyz');
        $user->serialize()->willReturn('serialized_user');

        $session->set('_security_user', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');

        $minkSession->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn('sylius@example.com', 'default', $minkSession);
    }

    function it_does_not_log_user_in_if_user_was_not_found(
        $userRepository,
        $session,
        UserInterface $user,
        Session $minkSession
    ) {
        $userRoles = ['ROLE_USER'];
        $userRepository->findOneBy(['username' => 'sylius@example.com'])->willReturn(null);
        $user->getRoles()->willReturn($userRoles);
        $user->getPassword()->willReturn('xyz');
        $user->serialize()->willReturn('serialized_user');

        $session->set('_security_user', Argument::any())->shouldNotBeCalled();
        $session->save()->shouldNotBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');

        $minkSession->setCookie('MOCKEDSID', 'xyzc123')->shouldNotBeCalled();
        $this->shouldThrow(new \InvalidArgumentException(sprintf('There is no user with email sylius@example.com')))->during('logIn', ['sylius@example.com', 'default', $minkSession]);
    }

    function it_logs_in_default_user_if_already_created(
        $userRepository,
        $session,
        UserInterface $user,
        Session $minkSession
    ) {
        $userRepository->findOneBy(['username' => 'john.doe@example.com'])->willReturn($user);

        $user->getRoles()->willReturn(['ROLE_USER']);
        $user->getPassword()->willReturn('password123');
        $user->serialize()->willReturn('serialized_user');

        $session->set('_security_user', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');

        $minkSession->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logInDefaultUser($minkSession);
    }

    function it_creates_default_user_if_it_does_not_exist_and_log_it_in(
        $userRepository,
        $session,
        $userFactory,
        UserInterface $user,
        Session $minkSession
    ) {
        $userRepository->findOneBy(['username' => 'john.doe@example.com'])->willReturn(null);
        $userFactory->create('John', 'Doe', 'john.doe@example.com', 'password123')->willReturn($user);
        $userRepository->add($user)->shouldBeCalled();

        $user->getRoles()->willReturn(['ROLE_USER']);
        $user->getPassword()->willReturn('password123');
        $user->serialize()->willReturn('serialized_user');

        $session->set('_security_user', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');

        $minkSession->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logInDefaultUser($minkSession)->shouldReturn($user);
    }
}
