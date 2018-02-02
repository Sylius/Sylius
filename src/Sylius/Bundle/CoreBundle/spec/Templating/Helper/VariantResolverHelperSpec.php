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

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Templating\Helper\Helper;

final class VariantResolverHelperSpec extends ObjectBehavior
{
    function let(ProductVariantResolverInterface $productVariantResolver): void
    {
        $this->beConstructedWith($productVariantResolver);
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_null_if_product_has_no_variants(
        ProductVariantResolverInterface $productVariantResolver,
        ProductInterface $product
    ) {
        $productVariantResolver->getVariant($product)->willReturn(null);

        $this->resolveVariant($product)->shouldBeEqualTo(null);
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_resolve_variant');
    }
}
