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

namespace spec\Sylius\Bundle\ApiBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiOrdersSubSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;

final class ShopApiUriBasedSectionResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('/api/v2/shop', 'orders');
    }

    function it_is_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_returns_shop_api_section_if_path_starts_with_api_v2_shop(): void
    {
        $this->getSection('/api/v2/shop/something')->shouldBeLike(new ShopApiSection());
        $this->getSection('/api/v2/shop')->shouldBeLike(new ShopApiSection());
    }

    function it_returns_shop_api_orders_subsection_if_path_contains_orders(): void
    {
        $this->getSection('/api/v2/shop/orders')->shouldBeLike(new ShopApiOrdersSubSection());
    }

    function it_throws_an_exception_if_path_does_not_start_with_api_v2_shop(): void
    {
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/shop']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/admin']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/en_US/api']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/api/v1']);
        $this->shouldThrow(SectionCannotBeResolvedException::class)->during('getSection', ['/api/v2']);
    }
}
