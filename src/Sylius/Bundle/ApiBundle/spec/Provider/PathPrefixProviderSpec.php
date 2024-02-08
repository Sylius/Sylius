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

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class PathPrefixProviderSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext, '/api/v2');
    }

    function it_implements_a_path_prefix_provider_interface(): void
    {
        $this->shouldImplement(PathPrefixProviderInterface::class);
    }

    function it_returns_null_if_the_given_path_is_not_api_path(): void
    {
        $this->getPathPrefix('/old-api/shop/certain-route')->shouldReturn(null);
    }

    function it_returns_null_if_the_given_path_does_not_match_api_path(): void
    {
        $this->getPathPrefix('/api/v2/wrong/certain-route')->shouldReturn(null);
    }

    function it_returns_shop_prefix_based_on_the_given_path(): void
    {
        $this->getPathPrefix('/api/v2/shop/certain-route')->shouldReturn('shop');
    }

    function it_returns_admin_prefix_based_on_the_given_path(): void
    {
        $this->getPathPrefix('/api/v2/admin/certain-route')->shouldReturn('admin');
    }

    function it_returns_prefix_from_api_route_with_slashes(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext, '/api/long/route/name');
        $this->getPathPrefix('/api/long/route/name/admin/certain-route')->shouldReturn('admin');
    }

    function it_returns_admin_prefix_if_currently_logged_in_is_admin_user(
        UserContextInterface $userContext,
        AdminUserInterface $user,
    ): void {
        $userContext->getUser()->willReturn($user);

        $this->getCurrentPrefix()->shouldReturn('admin');
    }

    function it_returns_shop_prefix_if_currently_logged_in_is_shop_user(
        UserContextInterface $userContext,
        ShopUserInterface $user,
    ): void {
        $userContext->getUser()->willReturn($user);

        $this->getCurrentPrefix()->shouldReturn('shop');
    }

    function it_returns_shop_prefix_if_there_is_no_logged_in_user(UserContextInterface $userContext): void
    {
        $userContext->getUser()->willReturn(null);

        $this->getCurrentPrefix()->shouldReturn('shop');
    }
}
