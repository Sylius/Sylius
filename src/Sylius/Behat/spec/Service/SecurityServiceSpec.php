<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\SecurityService;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SecurityServiceSpec extends ObjectBehavior
{
    function let(
        SessionInterface $session,
        CookieSetterInterface $cookieSetter
    ) {
        $this->beConstructedWith($session, $cookieSetter, 'shop');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SecurityService::class);
    }

    function it_implements_security_service_interface()
    {
        $this->shouldImplement(SecurityServiceInterface::class);
    }

    function it_logs_user_in(
        SessionInterface $session,
        CookieSetterInterface $cookieSetter,
        ShopUserInterface $shopUser
    ) {
        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getPassword()->willReturn('xyz');
        $shopUser->serialize()->willReturn('serialized_user');

        $session->set('_security_shop', Argument::any())->shouldBeCalled();
        $session->save()->shouldBeCalled();

        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logIn($shopUser);
    }

    function it_logs_user_out(
        SessionInterface $session,
        CookieSetterInterface $cookieSetter
    ) {
        $session->set('_security_shop', null)->shouldBeCalled();
        $session->save()->shouldBeCalled();
        $session->getName()->willReturn('MOCKEDSID');
        $session->getId()->willReturn('xyzc123');
        $cookieSetter->setCookie('MOCKEDSID', 'xyzc123')->shouldBeCalled();

        $this->logOut();
    }

    function it_throws_token_not_found_exception(
        SessionInterface $session
    ) {
        $session->get('_security_shop')->willReturn(null);

        $this->shouldThrow(TokenNotFoundException::class)->during('getCurrentToken');
    }
}
