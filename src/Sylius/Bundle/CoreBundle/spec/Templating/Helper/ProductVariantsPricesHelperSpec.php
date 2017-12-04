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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

final class ProductVariantsPricesHelperSpec extends ObjectBehavior
{
    function let(ProductVariantsPricesProviderInterface $productVariantsPricesProvider): void
    {
        $this->beConstructedWith($productVariantsPricesProvider);
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_provider_to_get_variants_prices(
        ChannelInterface $channel,
        ProductInterface $product,
        ProductVariantsPricesProviderInterface $productVariantsPricesProvider
    ): void {
        $productVariantsPricesProvider->provideVariantsPrices($product, $channel)->willReturn([
            ['color' => 'black', 'value' => 1000],
        ]);

        $this->getPrices($product, $channel)->shouldReturn([['color' => 'black', 'value' => 1000]]);
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_product_variants_prices');
    }
}
