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

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

final class PercentageGenerationPolicySpec extends ObjectBehavior
{
    function let(PromotionCouponRepositoryInterface $couponRepository): void
    {
        $this->beConstructedWith($couponRepository, 0.5);
    }

    function it_implements_a_generator_validator_interface(): void
    {
        $this->shouldImplement(GenerationPolicyInterface::class);
    }

    function it_examines_possibility_of_coupon_generation(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository,
    ): void {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $instruction->getPrefix()->willReturn(null);
        $instruction->getSuffix()->willReturn(null);
        $couponRepository->countByCodeLength(1, null, null)->willReturn(0);

        $this->isGenerationPossible($instruction)->shouldReturn(false);
    }

    function it_examines_possibility_of_coupon_generation_with_prefix_and_suffix(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository,
    ): void {
        $instruction->getAmount()->willReturn(7);
        $instruction->getCodeLength()->willReturn(1);
        $instruction->getPrefix()->willReturn('CHRISTMAS_');
        $instruction->getSuffix()->willReturn('_SALE');
        $couponRepository
            ->countByCodeLength(1, 'CHRISTMAS_', '_SALE')
            ->willReturn(0)
        ;

        $this->isGenerationPossible($instruction)->shouldReturn(true);
    }

    function it_returns_possible_generation_amount(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository,
    ): void {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $instruction->getPrefix()->willReturn(null);
        $instruction->getSuffix()->willReturn(null);
        $couponRepository->countByCodeLength(1, null, null)->willReturn(1);

        $this->getPossibleGenerationAmount($instruction)->shouldReturn(7);
    }

    function it_returns_php_int_max_value_as_possible_generation_amount_when_code_length_is_too_large(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository,
    ): void {
        $instruction->getAmount()->willReturn(1000);
        $instruction->getCodeLength()->willReturn(40);
        $instruction->getPrefix()->willReturn(null);
        $instruction->getSuffix()->willReturn(null);
        $couponRepository->countByCodeLength(40, null, null)->willReturn(0);

        $this->getPossibleGenerationAmount($instruction)->shouldReturn(\PHP_INT_MAX);
    }

    function it_returns_possible_generation_amount_with_prefix_and_suffix(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository,
    ): void {
        $instruction->getAmount()->willReturn(3);
        $instruction->getCodeLength()->willReturn(1);
        $instruction->getPrefix()->willReturn('CHRISTMAS_');
        $instruction->getSuffix()->willReturn('_SALE');
        $couponRepository
            ->countByCodeLength(1, 'CHRISTMAS_', '_SALE')
            ->willReturn(5)
        ;

        $this->getPossibleGenerationAmount($instruction)->shouldReturn(3);
    }

    function it_throws_an_invalid_argument_exception_when_expected_amount_is_null(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
    ): void {
        $instruction->getAmount()->willReturn(null);
        $instruction->getCodeLength()->willReturn(1);

        $this->shouldThrow(\InvalidArgumentException::class)->during('isGenerationPossible', [$instruction]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getPossibleGenerationAmount', [$instruction]);
    }

    function it_throws_an_invalid_argument_exception_when_expecte_code_length_is_null(
        ReadablePromotionCouponGeneratorInstructionInterface $instruction,
    ): void {
        $instruction->getAmount()->willReturn(18);
        $instruction->getCodeLength()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('isGenerationPossible', [$instruction]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getPossibleGenerationAmount', [$instruction]);
    }
}
