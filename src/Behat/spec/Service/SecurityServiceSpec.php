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

namespace spec\Sylius\Behat\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\SecurityService;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        CookieSetterInterface $cookieSetter,
        SessionFactoryInterface $sessionFactory,
        SessionInterface $session,
    ): void {
        $sessionFactory->createSession()->willReturn($session);

        $this->beConstructedWith($requestStack, $cookieSetter, 'shop', $sessionFactory);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SecurityService::class);
    }

    function it_implements_security_service_interface(): void
    {
        $this->shouldImplement(SecurityServiceInterface::class);
    }

    function it_logs_user_in_when_session_factory_is_not_available(
        RequestStack $requestStack,
        CookieSetterInterface $cookieSetter,
        SessionInterface $session,
        ShopUserInterface $shopUser,
    ): void {
        $this->beConstructedWith($requestStack, $cookieSetter, 'shop');

        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getPassword()->willReturn('xyz');
        $shopUser->__serialize()->willReturn(['serialized_user']);

        $requestStack->getSession()->willReturn($session);
        $session->set('_security_shop', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();

        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn($shopUser);
    }

    function it_logs_user_in(
        RequestStack $requestStack,
        CookieSetterInterface $cookieSetter,
        SessionFactoryInterface $sessionFactory,
        SessionInterface $session,
        ShopUserInterface $shopUser,
    ): void {
        $sessionFactory->createSession()->willReturn($session);

        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getPassword()->willReturn('xyz');
        $shopUser->__serialize()->willReturn(['serialized_user']);

        $requestStack->push(Argument::type(Request::class))->shouldBeCalled();
        $requestStack->getSession()->willReturn($session);
        $session->set('_security_shop', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();

        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn($shopUser);
    }

    function it_does_nothing_when_there_is_no_session_during_log_out(
        RequestStack $requestStack,
        CookieSetterInterface $cookieSetter,
    ): void {
        $requestStack->getSession()->willThrow(SessionNotFoundException::class);

        $cookieSetter->setCookie(Argument::cetera())->shouldNotBeCalled();

        $this->logOut();
    }

    function it_logs_user_out(
        RequestStack $requestStack,
        SessionInterface $session,
        CookieSetterInterface $cookieSetter,
    ): void {
        $requestStack->getSession()->willReturn($session);
        $session->set('_security_shop', null)->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logOut();
    }

    function it_throws_token_not_found_exception(
        RequestStack $requestStack,
        SessionInterface $session,
    ): void {
        $requestStack->getSession()->willReturn($session);
        $session->get('_security_shop')->willReturn(null);

        $this->shouldThrow(TokenNotFoundException::class)->during('getCurrentToken');
    }
}
