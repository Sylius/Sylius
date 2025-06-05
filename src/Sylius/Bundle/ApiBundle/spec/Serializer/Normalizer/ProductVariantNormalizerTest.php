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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductVariantNormalizerTest extends TestCase
{
    private MockObject&ProductVariantPricesCalculatorInterface $pricesCalculator;

    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private MockObject&SectionProviderInterface $sectionProvider;

    private IriConverterInterface&MockObject $iriConverter;

    private ProductVariantNormalizer $productVariantNormalizer;

    private const ALREADY_CALLED = 'sylius_product_variant_normalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->productVariantNormalizer = new ProductVariantNormalizer($this->pricesCalculator, $this->availabilityChecker, $this->sectionProvider, $this->iriConverter, ['sylius:product_variant:index']);
    }

    public function testSupportsOnlyProductVariantInterface(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        self::assertTrue($this->productVariantNormalizer->supportsNormalization($variantMock, null, ['groups' => ['sylius:product_variant:index']]));
        self::assertFalse($this->productVariantNormalizer->supportsNormalization($orderMock, null, ['groups' => ['sylius:product_variant:index']]));
    }

    public function testSupportsNormalizationIfSectionIsNotAdminGet(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        self::assertTrue($this->productVariantNormalizer->supportsNormalization($variantMock, null, [
                'groups' => ['sylius:product_variant:index'],
            ]))
        ;
    }

    public function testDoesNotSupportIfSectionIsAdminGet(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        self::assertFalse($this->productVariantNormalizer->supportsNormalization($variantMock, null, [
                'groups' => ['sylius:product_variant:index'],
            ]))
        ;
    }

    public function testDoesNotSupportIfSerializationGroupIsNotSupported(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        self::assertFalse($this->productVariantNormalizer->supportsNormalization($variantMock, null, [
                'groups' => ['sylius:product_variant:show'],
            ]))
        ;
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        self::assertFalse($this->productVariantNormalizer
            ->supportsNormalization($variantMock, null, [
                'sylius_product_variant_normalizer_already_called' => true,
                'groups' => ['sylius:product_variant:index'],
            ]))
        ;
    }

    public function testSerializesProductVariantIfItemOperationNameIsDifferentThatAdminGet(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::once())->method('calculate')->with($variantMock, ['channel' => $channelMock])->willReturn(1000);
        $this->pricesCalculator->expects(self::once())->method('calculateOriginal')->with($variantMock, ['channel' => $channelMock])->willReturn(1000);
        $this->pricesCalculator->expects(self::once())->method('calculateLowestPriceBeforeDiscount')->with($variantMock, ['channel' => $channelMock])->willReturn(500);
        $variantMock->expects(self::once())->method('getAppliedPromotionsForChannel')->with($channelMock)->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        $this->productVariantNormalizer->expects(self::once())->method('normalize')->with($variantMock, null, [
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])
            ->shouldBeLike(['price' => 1000, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 500, 'inStock' => true])
        ;
    }

    public function testReturnsOriginalPriceIfIsDifferentThanPrice(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::once())->method('calculate')->with($variantMock, ['channel' => $channelMock])->willReturn(500);
        $this->pricesCalculator->expects(self::once())->method('calculateOriginal')->with($variantMock, ['channel' => $channelMock])->willReturn(1000);
        $this->pricesCalculator->expects(self::once())->method('calculateLowestPriceBeforeDiscount')->with($variantMock, ['channel' => $channelMock])->willReturn(100);
        $variantMock->expects(self::once())->method('getAppliedPromotionsForChannel')->with($channelMock)->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        $this->productVariantNormalizer->expects(self::once())->method('normalize')->with($variantMock, null, [
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])
            ->shouldBeLike(['price' => 500, 'originalPrice' => 1000, 'lowestPriceBeforeDiscount' => 100, 'inStock' => true])
        ;
    }

    public function testReturnsCatalogPromotionsIfApplied(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var CatalogPromotionInterface|MockObject $catalogPromotionMock */
        $catalogPromotionMock = $this->createMock(CatalogPromotionInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::once())->method('calculate')->with($variantMock, ['channel' => $channelMock])->willReturn(500);
        $this->pricesCalculator->expects(self::once())->method('calculateOriginal')->with($variantMock, ['channel' => $channelMock])->willReturn(1000);
        $this->pricesCalculator->expects(self::once())->method('calculateLowestPriceBeforeDiscount')->with($variantMock, ['channel' => $channelMock])->willReturn(100);
        $catalogPromotionMock->expects(self::once())->method('getCode')->willReturn('winter_sale');
        $variantMock->expects(self::once())->method('getAppliedPromotionsForChannel')->with($channelMock)->willReturn(new ArrayCollection([$catalogPromotionMock]));
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        $this->iriConverter->expects(self::once())->method('getIriFromResource')->with($catalogPromotionMock, UrlGeneratorInterface::ABS_PATH, null, [ContextKeys::CHANNEL => $channelMock, self::ALREADY_CALLED => true, 'groups' => ['sylius:product_variant:index']])
            ->willReturn('/api/v2/shop/catalog-promotions/winter_sale')
        ;
        $this->productVariantNormalizer->expects(self::once())->method('normalize')->with($variantMock, null, [
            ContextKeys::CHANNEL => $channelMock,
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

    public function testDoesntReturnPricesAndPromotionsWhenChannelKeyIsNotInTheContext(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::never())->method('calculate')->with($this->any());
        $this->pricesCalculator->expects(self::never())->method('calculateOriginal')->with($this->any());
        $variantMock->expects(self::never())->method('getAppliedPromotionsForChannel');
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        self::assertSame(['inStock' => true], $this->productVariantNormalizer->normalize($variantMock, null, ['groups' => ['sylius:product_variant:index']]));
    }

    public function testDoesntReturnPricesAndPromotionsWhenChannelFromContextIsNull(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => null,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::never())->method('calculate')->with($this->any());
        $this->pricesCalculator->expects(self::never())->method('calculateOriginal')->with($this->any());
        $variantMock->expects(self::never())->method('getAppliedPromotionsForChannel');
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        self::assertSame(['inStock' => true], $this->productVariantNormalizer->normalize($variantMock, null, [
            ContextKeys::CHANNEL => null,
            'groups' => ['sylius:product_variant:index'],
        ]));
    }

    public function testDoesntReturnPricesIfChannelConfigurationIsNotFound(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::once())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::once())->method('calculate')->with($variantMock, ['channel' => $channelMock])->willThrowException(MissingChannelConfigurationException::class);
        $this->pricesCalculator->expects(self::once())->method('calculateOriginal')->with($variantMock, ['channel' => $channelMock])->willThrowException(MissingChannelConfigurationException::class);
        $variantMock->expects(self::once())->method('getAppliedPromotionsForChannel')->with($channelMock)->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())->method('isStockAvailable')->with($variantMock)->willReturn(true);
        self::assertSame(['inStock' => true], $this->productVariantNormalizer->normalize($variantMock, null, [
            ContextKeys::CHANNEL => $channelMock,
            'groups' => ['sylius:product_variant:index'],
        ]));
    }

    public function testThrowsAnExceptionIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        $this->productVariantNormalizer->setNormalizer($normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $normalizerMock->expects(self::never())->method('normalize')->with($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->productVariantNormalizer->normalize($variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ]);
    }
}
