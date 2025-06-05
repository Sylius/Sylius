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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\NumericToStringDenormalizer;
use Sylius\Component\Core\Model\TaxRateInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class NumericToStringDenormalizerTest extends TestCase
{
    private DenormalizerInterface&MockObject $denormalizer;

    private NumericToStringDenormalizer $numericToStringDenormalizer;

    public const ALREADY_CALLED =
        'sylius_numeric_to_string_denormalizer_already_called_for_Sylius\Component\Core\Model\TaxRateInterface';

    protected function setUp(): void
    {
        parent::setUp();
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->numericToStringDenormalizer = new NumericToStringDenormalizer(
            TaxRateInterface::class,
            'amount',
        );
        $this->numericToStringDenormalizer->setDenormalizer($this->denormalizer);
    }

    public function testSupportsDenormalizationOfTaxRateWithAmountSet(): void
    {
        self::assertFalse(
            $this->numericToStringDenormalizer->supportsDenormalization(['amount' => 0.23], \stdClass::class),
        );

        self::assertFalse(
            $this->numericToStringDenormalizer->supportsDenormalization(0.23, TaxRateInterface::class),
        );

        self::assertFalse(
            $this->numericToStringDenormalizer->supportsDenormalization([], TaxRateInterface::class),
        );

        self::assertFalse(
            $this->numericToStringDenormalizer->supportsDenormalization(
                ['amount' => 0.23],
                TaxRateInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            ),
        );

        self::assertTrue(
            $this->numericToStringDenormalizer->supportsDenormalization(['amount' => 0.23], TaxRateInterface::class),
        );
    }

    public function testDenormalizesTaxRateChangingFloatAmountToString(): void
    {
        /** @var TaxRateInterface|MockObject $taxRateMock */
        $taxRateMock = $this->createMock(TaxRateInterface::class);

        $this->denormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(['amount' => '0.23'], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRateMock);

        self::assertSame(
            $taxRateMock,
            $this->numericToStringDenormalizer->denormalize(['amount' => 0.23], TaxRateInterface::class),
        );
    }

    public function testDenormalizesTaxRateChangingIntAmountToString(): void
    {
        /** @var TaxRateInterface|MockObject $taxRateMock */
        $taxRateMock = $this->createMock(TaxRateInterface::class);

        $this->denormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(['amount' => '12'], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRateMock);

        self::assertSame(
            $taxRateMock,
            $this->numericToStringDenormalizer->denormalize(['amount' => 12], TaxRateInterface::class),
        );
    }
}
