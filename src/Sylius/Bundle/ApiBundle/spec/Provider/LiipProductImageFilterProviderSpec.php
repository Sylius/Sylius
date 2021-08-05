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
use Sylius\Bundle\ApiBundle\Provider\ProductImageFilterProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LiipProductImageFilterProviderSpec extends ObjectBehavior
{
    function it_is_a_product_image_filter_provider(): void
    {
        $this->shouldImplement(ProductImageFilterProviderInterface::class);
    }

    function it_returns_all_image_filters(ContainerInterface $container): void
    {
        $filters = ['sylius_shop_product_original' => 'args', 'sylius_admin_product_original' => 'args'];
        $container->setParameter('liip_imagine.filter_sets', $filters);
        $this->setContainer($container);

        $container->getParameter('liip_imagine.filter_sets')->willReturn($filters);

        $this->provideAllFilters()->shouldReturn($filters);
    }

    function it_returns_shop_image_filters(ContainerInterface $container): void
    {
        $filters = ['sylius_shop_product_original' => 'args', 'sylius_admin_product_original' => 'args'];
        $container->setParameter('liip_imagine.filter_sets', $filters);
        $this->setContainer($container);

        $container->getParameter('liip_imagine.filter_sets')->willReturn($filters);

        $this->provideShopFilters()->shouldReturn(['sylius_shop_product_original' => 'args']);
    }
}
