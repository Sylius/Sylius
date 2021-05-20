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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Model\Order;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductVariantNormalizerSpec extends ObjectBehavior
{
    function let(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        ChannelContextInterface $channelContext,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $this->beConstructedWith($pricesCalculator, $channelContext, $availabilityChecker);
    }

    function it_supports_only_product_variant_interface(): void
    {
        $this->supportsNormalization(new ProductVariant())->shouldReturn(true);
        $this->supportsNormalization(new Order())->shouldReturn(false);
    }

    function it_does_not_serialize_if_item_operation_name_is_admin_get(): void
    {
        $this->supportsNormalization(new ProductVariant(), null, ['item_operation_name' => 'admin_get'])->shouldReturn(false);
    }

    function it_serializes_product_variant_if_item_operation_name_is_different_that_admin_get(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        ChannelContextInterface $channelContext,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, ['product_variant_normalizer_already_called' => true])->willReturn([]);

        $channelContext->getChannel()->willReturn($channel);
        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(1000);
        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [])->shouldReturn(['price' => 1000, 'inStock' => true]);
    }
}
