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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductAttributeValueNormalizer;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductAttributeValueNormalizerTest extends TestCase
{
    private LocaleProviderInterface&MockObject $localeProvider;

    private ProductAttributeValueNormalizer $productAttributeValueNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->productAttributeValueNormalizer = new ProductAttributeValueNormalizer($this->localeProvider, 'en_US');
    }

    public function testSupportsOnlyProductAttributeValueInterface(): void
    {
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        self::assertTrue($this->productAttributeValueNormalizer->supportsNormalization($productAttributeValueMock));
        self::assertFalse($this->productAttributeValueNormalizer->supportsNormalization($orderMock));
    }

    public function testSupportsTheNormalizerHasNotCalledYet(): void
    {
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        self::assertTrue($this->productAttributeValueNormalizer
            ->supportsNormalization($productAttributeValueMock, null, []))
        ;
        self::assertFalse($this->productAttributeValueNormalizer
            ->supportsNormalization($productAttributeValueMock, null, ['sylius_product_attribute_value_normalizer_already_called' => true]))
        ;
    }

    public function testSerializesProductAttributeSelectValues(): void
    {
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeInterface|MockObject $productAttributeMock */
        $productAttributeMock = $this->createMock(ProductAttributeInterface::class);
        $normalizerMock->expects(self::once())->method('normalize')->with($productAttributeValueMock, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;
        $productAttributeValueMock->expects(self::once())->method('getType')->willReturn('select');
        $productAttributeValueMock->expects(self::once())->method('getAttribute')->willReturn($productAttributeMock);
        $productAttributeMock->expects(self::once())->method('getConfiguration')->willReturn([
            'choices' => [
                'uuid1' => [
                    'de_DE' => 'de text1',
                    'pl_PL' => 'pl text1',
                    'fr_FR' => 'fr text1',
                    'en_US' => 'en text1',
                    'zu_ZA' => 'zu text1',
                ],
                'uuid2' => [
                    'de_DE' => 'de text2',
                    'fr_FR' => 'fr text2',
                    'en_US' => 'en text2',
                    'zu_ZA' => 'zu text2',
                ],
                'uuid3' => [
                    'de_DE' => 'de text3',
                    'en_US' => 'en text3',
                    'zu_ZA' => 'zu text3',
                ],
                'uuid4' => [
                    'de_DE' => 'de text4',
                    'zu_ZA' => 'zu text4',
                ],
            ],
        ]);
        $productAttributeValueMock->expects(self::once())->method('getValue')->willReturn(['uuid1', 'uuid2', 'uuid3', 'uuid4']);
        $productAttributeValueMock->expects(self::once())->method('getLocaleCode')->willReturn('pl_PL');
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('fr_FR');
        $this->productAttributeValueNormalizer->setNormalizer($normalizerMock);
        self::assertSame([
            'value' => [
                'pl text1',
                'fr text2',
                'en text3',
                'de text4',
            ],
        ], $this->productAttributeValueNormalizer->normalize($productAttributeValueMock, null, []));
    }

    public function testSerializesProductAttributeSelectValuesWhenAttributeHasNoValue(): void
    {
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeInterface|MockObject $productAttributeMock */
        $productAttributeMock = $this->createMock(ProductAttributeInterface::class);
        $normalizerMock->expects(self::once())->method('normalize')->with($productAttributeValueMock, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;
        $productAttributeValueMock->expects(self::once())->method('getType')->willReturn('select');
        $productAttributeValueMock->expects(self::once())->method('getAttribute')->willReturn($productAttributeMock);
        $productAttributeValueMock->expects(self::once())->method('getValue')->willReturn(null);
        $productAttributeMock->expects(self::never())->method('getConfiguration');
        $productAttributeValueMock->expects(self::never())->method('getLocaleCode');
        $this->localeProvider->expects(self::never())->method('getDefaultLocaleCode');
        $this->productAttributeValueNormalizer->setNormalizer($normalizerMock);
        self::assertSame(['value' => []], $this->productAttributeValueNormalizer->normalize($productAttributeValueMock, null, []));
    }

    public function testSerializesProductAttributeDateValues(): void
    {
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeInterface|MockObject $productAttributeMock */
        $productAttributeMock = $this->createMock(ProductAttributeInterface::class);
        $normalizerMock->expects(self::once())->method('normalize')->with($productAttributeValueMock, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn([])
        ;
        $productAttributeValueMock->expects(self::once())->method('getType')->willReturn('date');
        $productAttributeValueMock->expects(self::once())->method('getAttribute')->willReturn($productAttributeMock);
        $productAttributeValueMock->expects(self::once())->method('getValue')->willReturn(new \DateTime('2022-01-01 14:16:53'));
        $productAttributeValueMock->expects(self::once())->method('getLocaleCode')->willReturn('pl_PL');
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('fr_FR');
        $this->productAttributeValueNormalizer->setNormalizer($normalizerMock);
        self::assertSame([
            'value' => '2022-01-01',
        ], $this->productAttributeValueNormalizer->normalize($productAttributeValueMock, null, []));
    }

    public function testDoesNotChangeTheValueOnIntegerType(): void
    {
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);
        /** @var ProductAttributeValueInterface|MockObject $productAttributeValueMock */
        $productAttributeValueMock = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeInterface|MockObject $productAttributeMock */
        $productAttributeMock = $this->createMock(ProductAttributeInterface::class);
        $normalizerMock->expects(self::once())->method('normalize')->with($productAttributeValueMock, null, ['sylius_product_attribute_value_normalizer_already_called' => true])
            ->willReturn(['value' => 42])
        ;
        $productAttributeValueMock->expects(self::once())->method('getType')->willReturn('integer');
        $this->productAttributeValueNormalizer->setNormalizer($normalizerMock);
        self::assertSame(['value' => 42], $this->productAttributeValueNormalizer->normalize($productAttributeValueMock));
    }
}
