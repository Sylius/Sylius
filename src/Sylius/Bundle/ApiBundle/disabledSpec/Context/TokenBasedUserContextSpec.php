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

namespace spec\Sylius\Bundle\ApiBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenBasedUserContextSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage): void
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_implements_user_context_interface(): void
    {
        $this->shouldImplement(UserContextInterface::class);
    }

    function it_returns_user_from_token(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->getUser()->shouldReturn($user);
    }

    function it_returns_null_if_user_from_token_is_anonymous(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->getUser()->shouldReturn(null);
    }

    function it_returns_null_if_user_from_token_is_null(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->getUser()->shouldReturn(null);
    }

    function it_returns_null_if_no_token_is_set_in_token_storage(TokenStorageInterface $tokenStorage): void
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->getUser()->shouldReturn(null);
    }
}
