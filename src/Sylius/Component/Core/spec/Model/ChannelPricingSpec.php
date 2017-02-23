<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelPricingSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelPricing::class);
    }

    function it_implements_channel_pricing_interface()
    {
        $this->shouldImplement(ChannelPricingInterface::class);
    }

    function its_channel_code_is_mutable()
    {
        $this->setChannel('WEB');
        $this->getChannel()->shouldReturn('WEB');
    }

    function its_product_variant_is_mutable(ProductVariantInterface $productVariant)
    {
        $this->setProductVariant($productVariant);
        $this->getProductVariant()->shouldReturn($productVariant);
    }

    function its_price_is_mutable()
    {
        $this->setPrice(1000);
        $this->getPrice()->shouldReturn(1000);
    }
}
