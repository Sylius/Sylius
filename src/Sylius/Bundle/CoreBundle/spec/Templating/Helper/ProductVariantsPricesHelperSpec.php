<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantsPricesHelperSpec extends ObjectBehavior
{
    function let(ProductVariantsPricesProviderInterface $productVariantsPricesProvider)
    {
        $this->beConstructedWith($productVariantsPricesProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantsPricesHelper::class);
    }

    function it_is_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_provider_to_get_variants_prices(
        ChannelInterface $channel,
        ProductInterface $product,
        ProductVariantsPricesProviderInterface $productVariantsPricesProvider
    ) {
        $productVariantsPricesProvider->provideVariantsPrices($product, $channel)->willReturn([
            ['color' => 'black', 'value' => 1000]
        ]);

        $this->getPrices($product, $channel)->shouldReturn([['color' => 'black', 'value' => 1000]]);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_variants_prices');
    }
}
