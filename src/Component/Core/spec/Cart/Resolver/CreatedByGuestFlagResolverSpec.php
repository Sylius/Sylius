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

namespace spec\Sylius\Component\Core\Cart\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreatedByGuestFlagResolverSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage): void
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_implements_a_created_by_guest_flag_resolver_interface(): void
    {
        $this->shouldImplement(CreatedByGuestFlagResolverInterface::class);
    }

    function it_returns_false_if_there_is_logged_in_customer(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->resolveFlag()->shouldReturn(false);
    }

    function it_returns_true_if_order_is_created_by_anonymous_user(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->resolveFlag()->shouldReturn(true);
    }

    function it_returns_true_if_there_is_no_token(TokenStorageInterface $tokenStorage): void
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->resolveFlag()->shouldReturn(true);
    }
}
