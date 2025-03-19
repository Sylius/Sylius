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
use Sylius\Bundle\ApiBundle\Provider\PathPrefixes;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;

final class PathPrefixProviderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/api/v2', [PathPrefixes::ADMIN_PREFIX, PathPrefixes::SHOP_PREFIX]);
    }

    function it_implements_the_path_prefix_provider_interface(): void
    {
        $this->shouldImplement(PathPrefixProviderInterface::class);
    }

    function it_returns_null_if_the_given_path_is_not_api_path(): void
    {
        $this->getPathPrefix('/old-api/shop/certain-route')->shouldReturn(null);
    }

    function it_returns_null_if_the_given_path_does_not_match_api_prefixes(): void
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

    function it_returns_prefix_from_api_route_with_slashes(): void
    {
        $this->beConstructedWith('/api/long/route/name', [PathPrefixes::ADMIN_PREFIX]);

        $this->getPathPrefix('/api/long/route/name/admin/certain-route')->shouldReturn('admin');
    }
}
