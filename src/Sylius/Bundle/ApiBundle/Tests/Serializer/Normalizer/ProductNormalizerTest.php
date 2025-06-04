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
use Doctrine\Common\Collections\ArrayCollection;
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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductNormalizerTest extends TestCase
{
    private MockObject&ProductVariantResolverInterface $defaultProductVariantResolver;

    private IriConverterInterface&MockObject $iriConverter;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&NormalizerInterface $normalizer;

    private ProductNormalizer $productNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultProductVariantResolver = $this->createMock(ProductVariantResolverInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->productNormalizer = new ProductNormalizer(
            $this->defaultProductVariantResolver,
            $this->iriConverter,
            $this->sectionProvider,
            ['sylius:product:index'],
        );
        $this->productNormalizer->setNormalizer($this->normalizer);
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

        $this->sectionProvider->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($productMock, null, [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ])
            ->willReturn([]);

        $productMock->expects(self::once())
            ->method('getEnabledVariants')
            ->willReturn(new ArrayCollection([$variantMock]));

        $this->defaultProductVariantResolver->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn($variantMock);

        $callCount = 0;
        $this->iriConverter->expects($this->exactly(2))
            ->method('getIriFromResource')
            ->willReturnCallback(function ($variant) use ($variantMock, &$callCount) {
                switch ($callCount++) {
                    case 0:
                        self::assertSame($variantMock, $variant);

                        return '/api/v2/shop/product-variants/CODE';
                    case 1:
                        self::assertSame($variantMock, $variant);

                        return '/api/v2/shop/product-variants/CODE';
                }
            });

        self::assertSame(
            [
                'variants' => ['/api/v2/shop/product-variants/CODE'],
                'defaultVariant' => '/api/v2/shop/product-variants/CODE',
            ],
            $this->productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]),
        );
    }

    public function testAddsDefaultVariantFieldWithNullValueToSerializedProductIfThereIsNoDefaultVariant(): void
    {
        /** @var ProductVariantInterface|MockObject $variantMock */
        $variantMock = $this->createMock(ProductVariantInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $productMock,
                null,
                [
                'sylius_product_normalizer_already_called' => true,
                'groups' => ['sylius:product:index'],
            ],
            )->willReturn([]);

        $this->iriConverter->expects(self::once())
            ->method('getIriFromResource')
            ->with($variantMock)
            ->willReturn('/api/v2/shop/product-variants/CODE');

        $productMock->expects(self::once())
            ->method('getEnabledVariants')
            ->willReturn(new ArrayCollection([$variantMock]));

        $this->defaultProductVariantResolver->expects(self::once())
            ->method('getVariant')
            ->with($productMock)
            ->willReturn(null);

        self::assertSame([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => null,
        ], $this->productNormalizer->normalize($productMock, null, ['groups' => ['sylius:product:index']]));
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

        $this->normalizer->expects(self::never())
            ->method('normalize')
            ->with($productMock, null, [
            'groups' => ['sylius:product:show'],
        ]);

        self::expectException(InvalidArgumentException::class);

        $this->productNormalizer->normalize($productMock, null, [
            'groups' => ['sylius:product:show'],
        ]);
    }
}
