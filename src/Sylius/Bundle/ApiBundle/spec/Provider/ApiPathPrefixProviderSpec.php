<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProviderInterface;

final class ApiPathPrefixProviderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/new-api');
    }

    function it_is_a_api_path_prefix_provider(): void
    {
        $this->shouldImplement(ApiPathPrefixProviderInterface::class);
    }

    function it_returns_null_if_its_not_api_path(): void
    {
        $this->getPathPrefix('/old-api/shop/certain-route')->shouldReturn(null);
    }

    function it_returns_null_if_its_not_matching_api_path(): void
    {
        $this->getPathPrefix('/new-api/wrong/certain-route')->shouldReturn(null);
    }

    function it_returns_shop_prefix(): void
    {
        $this->getPathPrefix('/new-api/shop/certain-route')->shouldReturn('shop');
    }

    function it_returns_admin_prefix(): void
    {
        $this->getPathPrefix('/new-api/admin/certain-route')->shouldReturn('admin');
    }
}
