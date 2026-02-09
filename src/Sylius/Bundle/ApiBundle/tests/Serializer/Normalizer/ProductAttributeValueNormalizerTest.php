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

    private MockObject&NormalizerInterface $normalizer;

    private MockObject&ProductAttributeValueInterface $productAttributeValue;

    private MockObject&ProductAttributeInterface $productAttribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->productAttributeValueNormalizer = new ProductAttributeValueNormalizer(
            $this->localeProvider,
            'en_US',
        );
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->productAttributeValue = $this->createMock(ProductAttributeValueInterface::class);
        $this->productAttribute = $this->createMock(ProductAttributeInterface::class);
    }

    public function testSupportsOnlyProductAttributeValueInterface(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        self::assertTrue($this->productAttributeValueNormalizer->supportsNormalization($this->productAttributeValue));
        self::assertFalse($this->productAttributeValueNormalizer->supportsNormalization($orderMock));
    }

    public function testSupportsTheNormalizerHasNotCalledYet(): void
    {
        self::assertTrue($this->productAttributeValueNormalizer
            ->supportsNormalization($this->productAttributeValue, null, []));
        self::assertFalse(
            $this->productAttributeValueNormalizer
            ->supportsNormalization(
                $this->productAttributeValue,
                null,
                ['sylius_product_attribute_value_normalizer_already_called' => true],
            ),
        );
    }

    public function testSerializesProductAttributeSelectValues(): void
    {
        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $this->productAttributeValue,
                null,
                ['sylius_product_attribute_value_normalizer_already_called' => true],
            )
            ->willReturn([]);
        $this->productAttributeValue->expects(self::once())->method('getType')->willReturn('select');
        $this->productAttributeValue->expects(self::once())
            ->method('getAttribute')
            ->willReturn($this->productAttribute);
        $this->productAttribute->expects(self::once())
            ->method('getConfiguration')
            ->willReturn([
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
        $this->productAttributeValue->expects(self::once())
            ->method('getValue')
            ->willReturn(['uuid1', 'uuid2', 'uuid3', 'uuid4']);
        $this->productAttributeValue->method('getLocaleCode')->willReturn('pl_PL');
        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('fr_FR');
        $this->productAttributeValueNormalizer->setNormalizer($this->normalizer);
        self::assertSame([
            'value' => [
                'pl text1',
                'fr text2',
                'en text3',
                'de text4',
            ],
        ], $this->productAttributeValueNormalizer->normalize($this->productAttributeValue, null, []));
    }

    public function testSerializesProductAttributeSelectValuesWhenAttributeHasNoValue(): void
    {
        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $this->productAttributeValue,
                null,
                ['sylius_product_attribute_value_normalizer_already_called' => true],
            )->willReturn([]);
        $this->productAttributeValue->expects(self::once())->method('getType')->willReturn('select');
        $this->productAttributeValue->method('getAttribute')->willReturn($this->productAttribute);
        $this->productAttributeValue->expects(self::once())->method('getValue')->willReturn(null);
        $this->productAttribute->expects(self::never())->method('getConfiguration');
        $this->productAttributeValue->expects(self::never())->method('getLocaleCode');
        $this->localeProvider->expects(self::never())->method('getDefaultLocaleCode');
        $this->productAttributeValueNormalizer->setNormalizer($this->normalizer);
        self::assertSame(
            ['value' => []],
            $this->productAttributeValueNormalizer->normalize($this->productAttributeValue, null, []),
        );
    }

    public function testSerializesProductAttributeDateValues(): void
    {
        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $this->productAttributeValue,
                null,
                ['sylius_product_attribute_value_normalizer_already_called' => true],
            )->willReturn([]);
        $this->productAttributeValue->method('getType')->willReturn('date');
        $this->productAttributeValue->method('getAttribute')->willReturn($this->productAttribute);
        $this->productAttributeValue->method('getValue')->willReturn(new \DateTime('2022-01-01 14:16:53'));
        $this->productAttributeValue->method('getLocaleCode')->willReturn('pl_PL');
        $this->localeProvider->method('getDefaultLocaleCode')->willReturn('fr_FR');
        $this->productAttributeValueNormalizer->setNormalizer($this->normalizer);
        self::assertSame([
            'value' => '2022-01-01',
        ], $this->productAttributeValueNormalizer->normalize($this->productAttributeValue, null, []));
    }

    public function testDoesNotChangeTheValueOnIntegerType(): void
    {
        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $this->productAttributeValue,
                null,
                ['sylius_product_attribute_value_normalizer_already_called' => true],
            )->willReturn(['value' => 42]);
        $this->productAttributeValue->expects(self::once())->method('getType')->willReturn('integer');
        $this->productAttributeValueNormalizer->setNormalizer($this->normalizer);
        self::assertSame(['value' => 42], $this->productAttributeValueNormalizer->normalize($this->productAttributeValue));
    }
}
