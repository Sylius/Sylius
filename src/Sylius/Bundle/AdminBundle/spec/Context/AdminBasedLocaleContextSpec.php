<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AdminBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class AdminBasedLocaleContextSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_implements_locale_context_interface()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_throws_locale_not_found_exception_when_there_is_no_token(TokenStorageInterface $tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_when_there_is_no_user_in_the_token(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token
    ) {
        $token->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_when_the_user_taken_from_token_is_not_an_admin(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        UserInterface $user
    ) {
        $token->getUser()->willReturn($user);
        $tokenStorage->getToken()->willReturn($token);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_returns_locale_of_currently_logged_admin_user(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $admin
    ) {
        $admin->getLocaleCode()->willReturn('en_US');
        $token->getUser()->willreturn($admin);
        $tokenStorage->getToken()->willReturn($token);

        $this->getLocaleCode()->shouldReturn('en_US');
    }
}
