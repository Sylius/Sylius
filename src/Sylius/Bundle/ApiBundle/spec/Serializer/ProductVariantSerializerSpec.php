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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Model\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductVariantSerializerSpec extends ObjectBehavior
{
    function let(
        NormalizerInterface $objectNormalizer,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        ChannelContextInterface $channelContext
    ): void {
        $this->beConstructedWith($objectNormalizer, $pricesCalculator, $channelContext);
    }

    function it_supports_only_product_variant_interface(): void {
        $variant = new ProductVariant();
        $this->supportsNormalization($variant)->shouldReturn(true);

        $order = new Order();
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_does_not_serialize_if_item_operation_name_is_admin_get(): void {
        $variant = new ProductVariant();
        $this->supportsNormalization($variant, null, ['item_operation_name' => 'admin_get'])->shouldReturn(false);
    }

    function it_serializes_product_variant_if_item_operation_name_is_different_that_admin_get(
        NormalizerInterface $objectNormalizer,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        ChannelInterface $channel,
        ChannelContextInterface $channelContext
    ): void {
        $variant = new ProductVariant();

        $objectNormalizer->normalize($variant, null, [])->willReturn([]);

        $channelContext->getChannel()->willReturn($channel);
        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(1000);

        $this->normalize($variant, null, [])->shouldReturn(['price' => 1000]);
    }
}
