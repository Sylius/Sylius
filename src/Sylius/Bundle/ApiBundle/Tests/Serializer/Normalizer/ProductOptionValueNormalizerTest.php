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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductOptionValueNormalizer;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductOptionValueNormalizerTest extends TestCase
{
    private MockObject&NormalizerInterface $normalizer;

    private MockObject&TranslatableEntityLocaleAssignerInterface $translatableEntityLocaleAssigner;

    private ProductOptionValueNormalizer $productOptionValueNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->translatableEntityLocaleAssigner = $this->createMock(TranslatableEntityLocaleAssignerInterface::class);
        $this->productOptionValueNormalizer = new ProductOptionValueNormalizer($this->translatableEntityLocaleAssigner);
        $this->productOptionValueNormalizer->setNormalizer($this->normalizer);
    }

    public function testAnAwareNormalizer(): void
    {
        self::assertInstanceOf(NormalizerAwareInterface::class, $this->productOptionValueNormalizer);
    }

    public function testSupportsOnlyProductOptionValueInterface(): void
    {
        /** @var ProductOptionValueInterface|MockObject $productOptionValueMock */
        $productOptionValueMock = $this->createMock(ProductOptionValueInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        self::assertTrue($this->productOptionValueNormalizer->supportsNormalization($productOptionValueMock));

        self::assertFalse($this->productOptionValueNormalizer->supportsNormalization($orderMock));
    }

    public function testSupportsTheNormalizerHasNotCalledYet(): void
    {
        /** @var ProductOptionValueInterface|MockObject $productOptionValueMock */
        $productOptionValueMock = $this->createMock(ProductOptionValueInterface::class);

        self::assertTrue($this->productOptionValueNormalizer
            ->supportsNormalization($productOptionValueMock, null, []));

        self::assertFalse(
            $this->productOptionValueNormalizer->supportsNormalization(
                $productOptionValueMock,
                null,
                ['sylius_product_option_value_normalizer_already_called' => true],
            ),
        );
    }

    public function testAssignsLocaleToTranslatableEntity(): void
    {
        /** @var ProductOptionValueInterface|MockObject $productOptionValueMock */
        $productOptionValueMock = $this->createMock(ProductOptionValueInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with(
                $productOptionValueMock,
                null,
                ['sylius_product_option_value_normalizer_already_called' => true],
            )
            ->willReturn([]);

        $this->translatableEntityLocaleAssigner->expects(self::once())
            ->method('assignLocale')
            ->with($productOptionValueMock);

        self::assertSame([], $this->productOptionValueNormalizer->normalize($productOptionValueMock, null, []));
    }

    public function testThrowsAnExceptionIfTheGivenObjectIsNotAProductOptionValueInterface(): void
    {
        self::expectException(InvalidArgumentException::class);

        $this->productOptionValueNormalizer->normalize(new \stdClass());
    }

    public function testThrowsAnExceptionIfTheNormalizerWasAlreadyCalled(): void
    {
        /** @var ProductOptionValueInterface|MockObject $productOptionValueMock */
        $productOptionValueMock = $this->createMock(ProductOptionValueInterface::class);

        self::expectException(InvalidArgumentException::class);

        $this->productOptionValueNormalizer->normalize(
            $productOptionValueMock,
            null,
            ['sylius_product_option_value_normalizer_already_called' => true],
        );
    }
}
