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
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductVariantNormalizerSpec extends ObjectBehavior
{
    function let(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        ChannelContextInterface $channelContext,
        AvailabilityCheckerInterface $availabilityChecker,
        SectionProviderInterface $sectionProvider
    ): void {
        $this->beConstructedWith($pricesCalculator, $channelContext, $availabilityChecker, $sectionProvider);
    }

    function it_supports_only_product_variant_interface(ProductVariantInterface $variant, OrderInterface $order): void
    {
        $this->supportsNormalization($variant)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_supports_normalization_if_section_is_not_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $this->supportsNormalization($variant)->shouldReturn(true);
    }

    function it_does_not_support_if_section_is_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $this->supportsNormalization($variant)->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(ProductVariantInterface $variant): void
    {
        $this
            ->supportsNormalization($variant, null, ['product_variant_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
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

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, ['product_variant_normalizer_already_called' => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$variant, null, ['product_variant_normalizer_already_called' => true]])
        ;
    }
}
