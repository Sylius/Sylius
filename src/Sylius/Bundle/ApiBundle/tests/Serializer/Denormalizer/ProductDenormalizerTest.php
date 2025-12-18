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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductDenormalizer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductDenormalizerTest extends TestCase
{
    private ProductDenormalizer $productDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productDenormalizer = new ProductDenormalizer();
    }

    private const ALREADY_CALLED = 'sylius_product_denormalizer_already_called';

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse($this->productDenormalizer
            ->supportsDenormalization([], ProductInterface::class, context: [self::ALREADY_CALLED => true]))
        ;
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse($this->productDenormalizer->supportsDenormalization('string', ProductInterface::class));
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotAProduct(): void
    {
        self::assertFalse($this->productDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testRemovesOptionsFromDataIfGivenProductHasVariantsDefined(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);

        $productMock->expects(self::once())
            ->method('getVariants')
            ->willReturn(new ArrayCollection([$productVariantMock]));

        $this->productDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(
                [],
                ProductInterface::class,
                null,
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $productMock,
                    self::ALREADY_CALLED => true,
                ],
            )
            ->willReturn($productMock)
        ;
        $this->productDenormalizer->denormalize(
            ['options' => ['/options/color']],
            ProductInterface::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $productMock],
        );
    }

    public function testDoesNotRemoveOptionsFromDataIfGivenProductHasNpVariantsDefined(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $productMock->expects(self::once())->method('getVariants')->willReturn(new ArrayCollection([]));

        $this->productDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(
                ['options' => ['/options/color']],
                ProductInterface::class,
                null,
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $productMock,
                    self::ALREADY_CALLED => true,
                ],
            )
            ->willReturn($productMock);

        $this->productDenormalizer->denormalize(
            ['options' => ['/options/color']],
            ProductInterface::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $productMock],
        );
    }

    public function testDoesNotRemoveOptionsFromDataIfThereIsNoObjectToPopulateInContextDefined(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);

        $this->productDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(
                ['options' => ['/options/color']],
                ProductInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->willReturn($productMock);

        $this->productDenormalizer->denormalize(['options' => ['/options/color']], ProductInterface::class);
    }

    public function testThrowsAnExceptionIfObjectToPopulateIsNotAProduct(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);

        $this->productDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::never())
            ->method('denormalize')
            ->with([], ProductInterface::class, null, [self::ALREADY_CALLED => true]);

        self::expectException(\InvalidArgumentException::class);

        $this->productDenormalizer->denormalize(
            ['options' => ['/options/color']],
            ProductInterface::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $productVariantMock],
        );
    }
}
