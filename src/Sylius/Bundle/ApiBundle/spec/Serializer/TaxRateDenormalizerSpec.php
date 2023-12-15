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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\TaxRateInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TaxRateDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_tax_rate_denormalizer_already_called';

    function let(DenormalizerInterface $denormalizer): void
    {
        $this->setDenormalizer($denormalizer);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], TaxRateInterface::class, context: [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', TaxRateInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_tax_rate(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_removes_amount_from_data_if_its_null(
        DenormalizerInterface $denormalizer,
        TaxRateInterface $taxRate,
    ): void {
        $denormalizer
            ->denormalize([], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRate)
        ;

        $this
            ->denormalize(
                ['amount' => null],
                TaxRateInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->shouldReturn($taxRate)
        ;
    }

    function it_does_nothing_when_amount_is_missing(
        DenormalizerInterface $denormalizer,
        TaxRateInterface $taxRate,
    ): void {
        $denormalizer
            ->denormalize([], TaxRateInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($taxRate)
        ;

        $this
            ->denormalize(
                [],
                TaxRateInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->shouldReturn($taxRate)
        ;
    }
}
