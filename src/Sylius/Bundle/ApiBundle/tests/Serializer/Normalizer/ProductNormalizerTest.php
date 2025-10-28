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
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductNormalizer;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductNormalizerTest extends TestCase
{
    private MockObject&ProductVariantResolverInterface $defaultProductVariantResolver;

    private IriConverterInterface&MockObject $iriConverter;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&NormalizerInterface $normalizer;

    private AbstractObjectNormalizer&MockObject $objectNormalizer;

    private ProductNormalizer $productNormalizer;

    protected function setUp(): void
    {
        $this->defaultProductVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->objectNormalizer = $this->createMock(AbstractObjectNormalizer::class);

        $this->productNormalizer = $this->createNormalizer();
    }

    public function testSupportsOnlyProductInterfaceAndShopApiSection(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $this->sectionProvider->expects($this->exactly(3))
            ->method('getSection')
            ->willReturnOnConsecutiveCalls(
                new ShopApiSection(),
                new ShopApiSection(),
                new AdminApiSection(),
            );

        self::assertFalse($this->productNormalizer
            ->supportsNormalization($orderMock, null, ['groups' => ['sylius:product:index']]));

        self::assertTrue($this->productNormalizer
            ->supportsNormalization($productMock, null, ['groups' => ['sylius:product:index']]));

        self::assertFalse($this->productNormalizer
            ->supportsNormalization($productMock, null, ['groups' => ['sylius:product:show']]));

        self::assertFalse($this->productNormalizer
            ->supportsNormalization($productMock, null, ['groups' => ['sylius:product:index']]));
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        self::assertFalse($this->productNormalizer
            ->supportsNormalization($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ]))
        ;
    }

    public function testAddsDefaultVariantIriToSerializedProduct(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->normalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn([])
        ;

        $this->defaultProductVariantResolver
            ->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn($variantMock)
        ;

        $this->iriConverter
            ->expects(self::once())
            ->method('getIriFromResource')
            ->with($variantMock)
            ->willReturn('/api/v2/shop/product-variants/CODE')
        ;

        self::assertSame(
            [
                'defaultVariant' => '/api/v2/shop/product-variants/CODE',
                'defaultVariantData' => null,
            ],
            $this->productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]),
        );
    }

    public function testAddsDefaultVariantDataToSerializedProduct(): void
    {
        $productNormalizer = $this->createNormalizer(true);

        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->normalizer
            ->expects(self::exactly(2))
            ->method('normalize')
            ->willReturnCallback(function (ProductInterface|ProductVariantInterface $subject) {
                return $subject instanceof ProductVariantInterface ? ['code' => 'CODE'] : [];
            })
        ;

        $this->defaultProductVariantResolver
            ->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn($variantMock)
        ;

        $this->objectNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($variantMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn(['code' => 'CODE'])
        ;

        $this->iriConverter
            ->expects(self::once())
            ->method('getIriFromResource')
            ->with($variantMock)
            ->willReturn('/api/v2/shop/product-variants/CODE')
        ;

        self::assertSame(
            [
                'defaultVariant' => '/api/v2/shop/product-variants/CODE',
                'defaultVariantData' => ['code' => 'CODE'],
            ],
            $productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]),
        );
    }

    public function testAddsDefaultVariantIriWhenVariantSerializationHasNoDataToSerializedProduct(): void
    {
        $productNormalizer = $this->createNormalizer(true);

        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->normalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn([])
        ;

        $this->defaultProductVariantResolver
            ->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn($variantMock)
        ;

        $this->objectNormalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($variantMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn([])
        ;

        $this->iriConverter
            ->expects(self::once())
            ->method('getIriFromResource')
            ->with($variantMock)
            ->willReturn('/api/v2/shop/product-variants/CODE')
        ;

        self::assertSame(
            [
                'defaultVariant' => '/api/v2/shop/product-variants/CODE',
                'defaultVariantData' => null,
            ],
            $productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]),
        );
    }

    public function testAddsDefaultVariantFieldWithNullValueToSerializedProductIfThereIsNoDefaultVariant(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->normalizer
            ->expects(self::once())
            ->method('normalize')
            ->with($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn([])
        ;

        $this->defaultProductVariantResolver
            ->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn(null)
        ;

        self::assertSame(
            [
                'defaultVariant' => null,
                'defaultVariantData' => null,
            ],
            $this->productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]),
        );
    }

    public function testThrowsAnExceptionIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ]);

        self::expectException(InvalidArgumentException::class);

        $this->productNormalizer->normalize($productMock, null, [
            'sylius_product_normalizer_already_called' => true,
            'groups' => ['sylius:product:index'],
        ]);
    }

    public function testThrowsAnExceptionIfSerializationGroupIsNotSupported(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);

        $this->normalizer
            ->expects(self::never())
            ->method('normalize')
            ->with($productMock, null, [
                'groups' => ['sylius:product:show'],
            ])
        ;

        self::expectException(InvalidArgumentException::class);

        $this->productNormalizer->normalize($productMock, null, [
            'groups' => ['sylius:product:show'],
        ]);
    }

    private function createNormalizer(bool $withObjectNormalizer = false): ProductNormalizer
    {
        $productNormalizer = new ProductNormalizer(
            $this->defaultProductVariantResolver,
            $this->iriConverter,
            $this->sectionProvider,
            ['sylius:product:index'],
            $withObjectNormalizer ? $this->objectNormalizer : null,
        );

        $productNormalizer->setNormalizer($this->normalizer);

        return $productNormalizer;
    }
}
