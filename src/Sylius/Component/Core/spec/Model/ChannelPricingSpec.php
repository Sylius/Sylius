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
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
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
        $this->setChannelCode('WEB');
        $this->getChannelCode()->shouldReturn('WEB');
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

    function it_does_not_have_original_price_by_default()
    {
        $this->getOriginalPrice()->shouldReturn(null);
    }

    function its_original_price_is_mutable()
    {
        $this->setOriginalPrice(2000);
        $this->getOriginalPrice()->shouldReturn(2000);
    }

    function its_price_can_be_reduced()
    {
        $this->setPrice(1000);
        $this->setOriginalPrice(2000);
        $this->isPriceReduced()->shouldReturn(true);
    }

    function its_price_is_not_reduced_when_does_not_have_original_price()
    {
        $this->setPrice(2000);
        $this->isPriceReduced()->shouldReturn(false);
    }

    function its_price_is_not_reduced_when_original_price_is_same_as_price()
    {
        $this->setPrice(2000);
        $this->setOriginalPrice(2000);
        $this->isPriceReduced()->shouldReturn(false);
    }

    function it_price_is_not_reduced_when_original_price_is_smaller_than_price()
    {
        $this->setPrice(2000);
        $this->setOriginalPrice(1500);
        $this->isPriceReduced()->shouldReturn(false);
    }
}
