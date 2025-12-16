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

    private MockObject&ShopApiSection $shopApiSectionMock;

    private MockObject&ProductVariantInterface $variantMock;

    private MockObject&NormalizerInterface $normalizerMock;

    private ChannelInterface&MockObject $channelMock;

    private const ALREADY_CALLED = 'sylius_product_variant_normalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->productVariantNormalizer = new ProductVariantNormalizer(
            $this->pricesCalculator,
            $this->availabilityChecker,
            $this->sectionProvider,
            $this->iriConverter,
            ['sylius:product_variant:index'],
        );
        $this->shopApiSectionMock = $this->createMock(ShopApiSection::class);
        $this->variantMock = $this->createMock(ProductVariantInterface::class);
        $this->normalizerMock = $this->createMock(NormalizerInterface::class);
        $this->channelMock = $this->createMock(ChannelInterface::class);
    }

    public function testSupportsOnlyProductVariantInterface(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        self::assertTrue(
            $this->productVariantNormalizer->supportsNormalization(
                $this->variantMock,
                null,
                ['groups' => ['sylius:product_variant:index']],
            ),
        );
        self::assertFalse(
            $this->productVariantNormalizer->supportsNormalization(
                $orderMock,
                null,
                ['groups' => ['sylius:product_variant:index']],
            ),
        );
    }

    public function testSupportsNormalizationIfSectionIsNotAdminGet(): void
    {
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        self::assertTrue(
            $this->productVariantNormalizer->supportsNormalization(
                $this->variantMock,
                null,
                ['groups' => ['sylius:product_variant:index']],
            ),
        );
    }

    public function testDoesNotSupportIfSectionIsAdminGet(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        self::assertFalse(
            $this->productVariantNormalizer->supportsNormalization(
                $this->variantMock,
                null,
                ['groups' => ['sylius:product_variant:index']],
            ),
        );
    }

    public function testDoesNotSupportIfSerializationGroupIsNotSupported(): void
    {
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        self::assertFalse(
            $this->productVariantNormalizer->supportsNormalization(
                $this->variantMock,
                null,
                ['groups' => ['sylius:product_variant:show']],
            ),
        );
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        self::assertFalse($this->productVariantNormalizer
            ->supportsNormalization($this->variantMock, null, [
                'sylius_product_variant_normalizer_already_called' => true,
                'groups' => ['sylius:product_variant:index'],
            ]));
    }

    public function testSerializesProductVariantIfItemOperationNameIsDifferentThatAdminGet(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(
                $this->variantMock,
                null,
                [
                    'sylius_product_variant_normalizer_already_called' => true,
                    ContextKeys::CHANNEL => $this->channelMock,
                    'groups' => ['sylius:product_variant:index'],
                ],
            )->willReturn([]);
        $this->pricesCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(1000);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateOriginal')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(1000);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateLowestPriceBeforeDiscount')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(500);
        $this->variantMock->expects(self::once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channelMock)
            ->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        $result = $this->productVariantNormalizer->normalize($this->variantMock, null, [
            ContextKeys::CHANNEL => $this->channelMock,
            'groups' => ['sylius:product_variant:index'],
        ]);
        self::assertSame(
            [
                'inStock' => true,
                'price' => 1000,
                'originalPrice' => 1000,
                'lowestPriceBeforeDiscount' => 500,
            ],
            $result,
        );
    }

    public function testReturnsOriginalPriceIfIsDifferentThanPrice(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())
            ->method('getSection')
            ->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with($this->variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => $this->channelMock,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(500);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateOriginal')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(1000);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateLowestPriceBeforeDiscount')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(100);
        $this->variantMock->expects(self::once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channelMock)
            ->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        $result = $this->productVariantNormalizer->normalize($this->variantMock, null, [
            ContextKeys::CHANNEL => $this->channelMock,
            'groups' => ['sylius:product_variant:index'],
        ]);
        self::assertSame(
            [
                'inStock' => true,
                'price' => 500,
                'originalPrice' => 1000,
                'lowestPriceBeforeDiscount' => 100,
            ],
            $result,
        );
    }

    public function testReturnsCatalogPromotionsIfApplied(): void
    {
        $catalogPromotionMock = $this->createMock(CatalogPromotionInterface::class);
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(
                $this->variantMock,
                null,
                [
                    'sylius_product_variant_normalizer_already_called' => true,
                    ContextKeys::CHANNEL => $this->channelMock,
                    'groups' => ['sylius:product_variant:index'],
            ],
            )->willReturn([]);
        $this->pricesCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(500);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateOriginal')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(1000);
        $this->pricesCalculator->expects(self::once())
            ->method('calculateLowestPriceBeforeDiscount')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willReturn(100);
        $catalogPromotionMock->method('getCode')->willReturn('winter_sale');
        $this->variantMock->expects(self::once())->method('getAppliedPromotionsForChannel')
            ->with($this->channelMock)
            ->willReturn(new ArrayCollection([$catalogPromotionMock]));
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        $this->iriConverter->expects(self::once())
            ->method('getIriFromResource')
            ->with(
                $catalogPromotionMock,
                UrlGeneratorInterface::ABS_PATH,
                null,
                [
                    ContextKeys::CHANNEL => $this->channelMock,
                    self::ALREADY_CALLED => true,
                    'groups' => ['sylius:product_variant:index'],
                ],
            )->willReturn('/api/v2/shop/catalog-promotions/winter_sale');
        $result = $this->productVariantNormalizer->normalize($this->variantMock, null, [
            ContextKeys::CHANNEL => $this->channelMock,
            'groups' => ['sylius:product_variant:index'],
        ]);
        self::assertSame(
            [
                'inStock' => true,
                'price' => 500,
                'originalPrice' => 1000,
                'lowestPriceBeforeDiscount' => 100,
                'appliedPromotions' => ['/api/v2/shop/catalog-promotions/winter_sale'],
            ],
            $result,
        );
    }

    public function testDoesntReturnPricesAndPromotionsWhenChannelKeyIsNotInTheContext(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())
            ->method('getSection')
            ->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with($this->variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::never())->method('calculate')->with($this->any());
        $this->pricesCalculator->expects(self::never())->method('calculateOriginal')->with($this->any());
        $this->variantMock->expects(self::never())->method('getAppliedPromotionsForChannel');
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        self::assertSame(
            ['inStock' => true],
            $this->productVariantNormalizer->normalize(
                $this->variantMock,
                null,
                ['groups' => ['sylius:product_variant:index'],
                ],
            ),
        );
    }

    public function testDoesntReturnPricesAndPromotionsWhenChannelFromContextIsNull(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with($this->variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            ContextKeys::CHANNEL => null,
            'groups' => ['sylius:product_variant:index'],
        ])->willReturn([]);
        $this->pricesCalculator->expects(self::never())->method('calculate')->with($this->any());
        $this->pricesCalculator->expects(self::never())->method('calculateOriginal')->with($this->any());
        $this->variantMock->expects(self::never())->method('getAppliedPromotionsForChannel');
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        self::assertSame(
            ['inStock' => true],
            $this->productVariantNormalizer->normalize(
                $this->variantMock,
                null,
                [
                ContextKeys::CHANNEL => null,
                'groups' => ['sylius:product_variant:index'],
                ],
            ),
        );
    }

    public function testDoesntReturnPricesIfChannelConfigurationIsNotFound(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::once())
            ->method('normalize')
            ->with(
                $this->variantMock,
                null,
                [
                    'sylius_product_variant_normalizer_already_called' => true,
                    ContextKeys::CHANNEL => $this->channelMock,
                    'groups' => ['sylius:product_variant:index'],
                ],
            )->willReturn([]);
        $missingChannelConfigException = new MissingChannelConfigurationException('Missing channel configuration');
        $this->pricesCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willThrowException($missingChannelConfigException);
        $this->pricesCalculator->method('calculateOriginal')
            ->with($this->variantMock, ['channel' => $this->channelMock])
            ->willThrowException($missingChannelConfigException);
        $this->variantMock->expects(self::once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channelMock)
            ->willReturn(new ArrayCollection());
        $this->availabilityChecker->expects(self::once())
            ->method('isStockAvailable')
            ->with($this->variantMock)
            ->willReturn(true);
        self::assertSame(
            ['inStock' => true],
            $this->productVariantNormalizer->normalize(
                $this->variantMock,
                null,
                [
                ContextKeys::CHANNEL => $this->channelMock,
                'groups' => ['sylius:product_variant:index'],
            ],
            ),
        );
    }

    public function testThrowsAnExceptionIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        $this->productVariantNormalizer->setNormalizer($this->normalizerMock);
        $this->sectionProvider->method('getSection')->willReturn($this->shopApiSectionMock);
        $this->normalizerMock->expects(self::never())
            ->method('normalize')
            ->with($this->variantMock, null, [
                'sylius_product_variant_normalizer_already_called' => true,
                'groups' => ['sylius:product_variant:index'],
            ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->productVariantNormalizer->normalize($this->variantMock, null, [
            'sylius_product_variant_normalizer_already_called' => true,
            'groups' => ['sylius:product_variant:index'],
        ]);
    }
}
