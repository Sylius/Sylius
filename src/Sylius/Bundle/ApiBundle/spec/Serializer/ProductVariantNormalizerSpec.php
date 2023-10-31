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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductVariantNormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_product_variant_normalizer_already_called';

    function let(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        SectionProviderInterface $sectionProvider,
        IriConverterInterface $iriConverter,
    ): void {
        $this->beConstructedWith($pricesCalculator, $availabilityChecker, $sectionProvider, $iriConverter);
    }

    function it_supports_only_product_variant_interface(ProductVariantInterface $variant, OrderInterface $order): void
    {
        $this->supportsNormalization($variant)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_supports_normalization_if_section_is_not_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $this->supportsNormalization($variant)->shouldReturn(true);
    }

    function it_does_not_support_if_section_is_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $this->supportsNormalization($variant)->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(ProductVariantInterface $variant): void
    {
        $this
            ->supportsNormalization($variant, null, ['sylius_product_variant_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_serializes_product_variant_if_item_operation_name_is_different_that_admin_get(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(500);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this
            ->normalize($variant, null, [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(['price' => 1000, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 500, 'inStock' => true])
        ;
    }

    function it_returns_original_price_if_is_different_than_price(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(500);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(100);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this
            ->normalize($variant, null, [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike(['price' => 500, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 100, 'inStock' => true])
        ;
    }

    function it_returns_catalog_promotions_if_applied(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        IriConverterInterface $iriConverter,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(500);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(100);
        $catalogPromotion->getCode()->willReturn('winter_sale');

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
        $availabilityChecker->isStockAvailable($variant)->willReturn(true);
        $iriConverter
            ->getIriFromResource($catalogPromotion, UrlGeneratorInterface::ABS_PATH, null, [ContextKeys::CHANNEL => $channel, self::ALREADY_CALLED => true])
            ->willReturn('/api/v2/shop/catalog-promotions/winter_sale')
        ;

        $this
            ->normalize($variant, null, [ContextKeys::CHANNEL => $channel])
            ->shouldBeLike([
                'price' => 500,
                'originalPrice' => 1000,
                'lowestPriceBeforeDiscount' => 100,
                'appliedPromotions' => ['/api/v2/shop/catalog-promotions/winter_sale'],
                'inStock' => true,
            ])
        ;
    }

    function it_doesnt_return_prices_and_promotions_when_channel_key_is_not_in_the_context(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, ['sylius_product_variant_normalizer_already_called' => true])->willReturn([]);

        $pricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();
        $pricesCalculator->calculateOriginal(Argument::cetera())->shouldNotBeCalled();
        $variant->getAppliedPromotionsForChannel(Argument::any())->shouldNotBeCalled();

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [])->shouldReturn(['inStock' => true]);
    }

    function it_doesnt_return_prices_and_promotions_when_channel_from_context_is_null(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => null,
        ])->willReturn([]);

        $pricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();
        $pricesCalculator->calculateOriginal(Argument::cetera())->shouldNotBeCalled();
        $variant->getAppliedPromotionsForChannel(Argument::any())->shouldNotBeCalled();

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [ContextKeys::CHANNEL => null])->shouldReturn(['inStock' => true]);
    }

    function it_doesnt_return_prices_if_channel_configuration_is_not_found(
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willThrow(MissingChannelConfigurationException::class);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willThrow(MissingChannelConfigurationException::class);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [ContextKeys::CHANNEL => $channel])->shouldReturn(['inStock' => true]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($variant, null, ['sylius_product_variant_normalizer_already_called' => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$variant, null, ['sylius_product_variant_normalizer_already_called' => true]])
        ;
    }
}
