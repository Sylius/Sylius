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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxRateInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class NumericToStringDenormalizerSpec extends ObjectBehavior
{
    const ALREADY_CALLED = 'sylius_numeric_to_string_denormalizer_already_called_for_Sylius\Component\Core\Model\TaxRateInterface';

    function let(DenormalizerInterface $denormalizer): void
    {
        $this->beConstructedWith('Sylius\Component\Core\Model\TaxRateInterface', 'amount');
        $this->setDenormalizer($denormalizer);
    }

    function it_supports_denormalization_of_tax_rate_with_amount_set(): void
    {
        $this
            ->supportsDenormalization(['amount' => 0.23], \stdClass::class)
            ->shouldReturn(false)
        ;
        $this
            ->supportsDenormalization(0.23, TaxRateInterface::class)
            ->shouldReturn(false)
        ;
        $this
            ->supportsDenormalization([], TaxRateInterface::class)
            ->shouldReturn(false)
        ;
        $this
            ->supportsDenormalization(['amount' => 0.23], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
        ;
        $this
            ->supportsDenormalization(['amount' => 0.23], TaxRateInterface::class)
            ->shouldReturn(true)
        ;
    }

    function it_denormalizes_tax_rate_changing_float_amount_to_string(
        DenormalizerInterface $denormalizer,
        TaxRateInterface $taxRate,
    ): void {
        $denormalizer->denormalize(['amount' => '0.23'], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRate)
        ;

        $this->denormalize(['amount' => 0.23], TaxRateInterface::class)->shouldReturn($taxRate);
    }

    function it_denormalizes_tax_rate_changing_int_amount_to_string(
        DenormalizerInterface $denormalizer,
        TaxRateInterface $taxRate,
    ): void {
        $denormalizer->denormalize(['amount' => '12'], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRate)
        ;

        $this->denormalize(['amount' => 12], TaxRateInterface::class)->shouldReturn($taxRate);
    }
}
