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
use Sylius\Bundle\ApiBundle\Provider\ProductImageFilterProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class LiipProductImageFilterProviderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(['sylius_shop_product_original' => 'args', 'sylius_admin_product_original' => 'args']);
    }

    function it_is_a_product_image_filter_provider(): void
    {
        $this->shouldImplement(ProductImageFilterProviderInterface::class);
    }

    function it_returns_all_image_filters(ContainerInterface $container): void
    {
        $this->provideAllFilters()->shouldReturn(['sylius_shop_product_original' => 'args', 'sylius_admin_product_original' => 'args']);
    }

    function it_returns_shop_image_filters(ContainerInterface $container): void
    {
        $this->provideShopFilters()->shouldReturn(['sylius_shop_product_original' => 'args']);
    }
}
