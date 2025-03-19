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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
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
        $this->beConstructedWith(
            $pricesCalculator,
            $availabilityChecker,
            $sectionProvider,
            $iriConverter,
            ['sylius:product_variant:index'],
        );
    }

    function it_supports_only_product_variant_interface(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantInterface $variant,
        OrderInterface $order,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $this->supportsNormalization($variant, null, ['groups' => ['sylius:product_variant:index']])->shouldReturn(true);
        $this->supportsNormalization($order, null, ['groups' => ['sylius:product_variant:index']])->shouldReturn(false);
    }

    function it_supports_normalization_if_section_is_not_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $this->supportsNormalization($variant, null, [
                'groups' => ['sylius:product_variant:index'],
            ])
            ->shouldReturn(true)
        ;
    }

    function it_does_not_support_if_section_is_admin_get(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $this->supportsNormalization($variant, null, [
                'groups' => ['sylius:product_variant:index'],
            ])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_if_serialization_group_is_not_supported(
        ProductVariantInterface $variant,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $this->supportsNormalization($variant, null, [
                'groups' => ['sylius:product_variant:show'],
            ])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(ProductVariantInterface $variant): void
    {
        $this
            ->supportsNormalization($variant, null, [
                'sylius_product_variant_normalizer_already_called' => true,
                'groups' => ['sylius:product_variant:index'],
            ])
            ->shouldReturn(false)
        ;
    }

    function it_serializes_product_variant_if_item_operation_name_is_different_that_admin_get(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(500);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this
            ->normalize($variant, null, [
                ContextKeys::CHANNEL => $channel,
                'groups' => ['sylius:product_variant:index'],
            ])
            ->shouldBeLike(['price' => 1000, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 500, 'inStock' => true])
        ;
    }

    function it_returns_original_price_if_is_different_than_price(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(500);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(100);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this
            ->normalize($variant, null, [
                ContextKeys::CHANNEL => $channel,
                'groups' => ['sylius:product_variant:index'],
            ])
            ->shouldBeLike(['price' => 500, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 100, 'inStock' => true])
        ;
    }

    function it_returns_catalog_promotions_if_applied(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        IriConverterInterface $iriConverter,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willReturn(500);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willReturn(1000);
        $pricesCalculator->calculateLowestPriceBeforeDiscount($variant, ['channel' => $channel])->willReturn(100);
        $catalogPromotion->getCode()->willReturn('winter_sale');

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
        $availabilityChecker->isStockAvailable($variant)->willReturn(true);
        $iriConverter
            ->getIriFromResource($catalogPromotion, UrlGeneratorInterface::ABS_PATH, null, [ContextKeys::CHANNEL => $channel, self::ALREADY_CALLED => true, 'groups' => ['sylius:product_variant:index']])
            ->willReturn('/api/v2/shop/catalog-promotions/winter_sale')
        ;

        $this
            ->normalize($variant, null, [
                ContextKeys::CHANNEL => $channel,
                'groups' => ['sylius:product_variant:index'],
            ])
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
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();
        $pricesCalculator->calculateOriginal(Argument::cetera())->shouldNotBeCalled();
        $variant->getAppliedPromotionsForChannel(Argument::any())->shouldNotBeCalled();

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, ['groups' => ['sylius:product_variant:index']])->shouldReturn(['inStock' => true]);
    }

    function it_doesnt_return_prices_and_promotions_when_channel_from_context_is_null(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => null,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();
        $pricesCalculator->calculateOriginal(Argument::cetera())->shouldNotBeCalled();
        $variant->getAppliedPromotionsForChannel(Argument::any())->shouldNotBeCalled();

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [
            ContextKeys::CHANNEL => null,
            'groups' => ['sylius:product_variant:index'],
        ])->shouldReturn(['inStock' => true]);
    }

    function it_doesnt_return_prices_if_channel_configuration_is_not_found(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        ProductVariantPricesCalculatorInterface $pricesCalculator,
        AvailabilityCheckerInterface $availabilityChecker,
        NormalizerInterface $normalizer,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channel,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);

        $pricesCalculator->calculate($variant, ['channel' => $channel])->willThrow(MissingChannelConfigurationException::class);
        $pricesCalculator->calculateOriginal($variant, ['channel' => $channel])->willThrow(MissingChannelConfigurationException::class);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $availabilityChecker->isStockAvailable($variant)->willReturn(true);

        $this->normalize($variant, null, [
            ContextKeys::CHANNEL => $channel,
            'groups' => ['sylius:product_variant:index'],
        ])->shouldReturn(['inStock' => true]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
    ): void {
        $this->setNormalizer($normalizer);
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $normalizer->normalize($variant, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$variant, null, [
                'sylius_product_variant_normalizer_already_called' => true,
                'groups' => ['sylius:product_variant:index'],
            ]])
        ;
    }
}
