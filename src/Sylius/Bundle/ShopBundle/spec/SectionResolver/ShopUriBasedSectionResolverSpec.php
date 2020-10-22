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

namespace spec\Sylius\Bundle\ShopBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;

final class ShopUriBasedSectionResolverSpec extends ObjectBehavior
{
    function it_it_uri_based_section_resolver(): void
    {
        $this->shouldImplement(UriBasedSectionResolverInterface::class);
    }

    function it_always_returns_shop(): void
    {
        $this->getSection('/api/something')->shouldBeLike(new ShopSection());
        $this->getSection('/api')->shouldBeLike(new ShopSection());
        $this->getSection('/ap')->shouldBeLike(new ShopSection());
        $this->getSection('/shop')->shouldBeLike(new ShopSection());
        $this->getSection('/admin/asd')->shouldBeLike(new ShopSection());
        $this->getSection('/en_US/api')->shouldBeLike(new ShopSection());
    }
}
