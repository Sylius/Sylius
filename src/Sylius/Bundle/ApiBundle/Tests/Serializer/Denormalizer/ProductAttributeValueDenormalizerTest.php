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

use ApiPlatform\Metadata\IriConverterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Exception\InvalidProductAttributeValueTypeException;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductAttributeValueDenormalizer;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductAttributeValueDenormalizerTest extends TestCase
{
    private IriConverterInterface&MockObject $iriConverter;

    private ProductAttributeValueDenormalizer $productAttributeValueDenormalizer;

    private const ALREADY_CALLED = 'sylius_product_attribute_value_denormalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->productAttributeValueDenormalizer = new ProductAttributeValueDenormalizer($this->iriConverter);
    }

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse(
            $this->productAttributeValueDenormalizer->supportsDenormalization(
                [],
                ProductAttributeValueInterface::class,
                context: [self::ALREADY_CALLED => true],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse(
            $this->productAttributeValueDenormalizer->supportsDenormalization(
                'string',
                ProductAttributeValueInterface::class,
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotAProductAttributeValue(): void
    {
        self::assertFalse($this->productAttributeValueDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testThrowsAnExceptionIfGivenValueIsInWrongType(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductAttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(ProductAttributeInterface::class);

        $this->iriConverter->expects(self::once())
            ->method('getResourceFromIri')
            ->with('/attributes/material')
            ->willReturn($attributeMock);

        $attributeMock->expects(self::once())->method('getStorageType')->willReturn('text');

        $attributeMock->expects(self::once())->method('getName')->willReturn('Material');

        $this->productAttributeValueDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::never())
            ->method('denormalize')
            ->with([], ProductAttributeValueInterface::class, null, [self::ALREADY_CALLED => true]);

        self::expectException(InvalidProductAttributeValueTypeException::class);

        $this->productAttributeValueDenormalizer->denormalize(
            ['attribute' => '/attributes/material', 'value' => 4],
            ProductAttributeValueInterface::class,
        );
    }

    public function testDenormalizesDataIfGivenValueIsInProperTypes(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var ProductAttributeInterface|MockObject $attributeMock */
        $attributeMock = $this->createMock(ProductAttributeInterface::class);
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);

        $this->iriConverter->method('getResourceFromIri')
            ->with('/attributes/material')
            ->willReturn($attributeMock);

        $attributeMock->expects(self::once())->method('getStorageType')->willReturn('text');

        $attributeMock->expects(self::once())->method('getType')->willReturn('text');

        $this->productAttributeValueDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(
                ['attribute' => '/attributes/material', 'value' => 'ceramic'],
                ProductAttributeValueInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->willReturn($productAttributeValueMock);

        $this->productAttributeValueDenormalizer->denormalize(
            ['attribute' => '/attributes/material', 'value' => 'ceramic'],
            ProductAttributeValueInterface::class,
        );
    }
}
