<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Generator\PercentageGenerationPolicy;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PercentageGenerationPolicySpec extends ObjectBehavior
{
    function let(PromotionCouponRepositoryInterface $couponRepository)
    {
        $this->beConstructedWith($couponRepository, 0.5);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PercentageGenerationPolicy::class);
    }

    function it_implements_a_generator_validator_interface()
    {
        $this->shouldImplement(GenerationPolicyInterface::class);
    }

    function it_examine_possibility_of_coupon_generation(
        PromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository
    ) {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $couponRepository->countByCodeLength(1)->shouldBeCalled();

        $this->isGenerationPossible($instruction)->shouldReturn(false);
    }

    function it_returns_possible_generation_amount(
        PromotionCouponGeneratorInstructionInterface $instruction,
        PromotionCouponRepositoryInterface $couponRepository
    ) {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $couponRepository->countByCodeLength(1)->willReturn(1);

        $this->isGenerationPossible($instruction)->shouldReturn(false);
        $this->getPossibleGenerationAmount($instruction)->shouldReturn(7.0);
    }

    function it_throws_an_invalid_argument_exception_when_expected_amount_is_null(
        PromotionCouponGeneratorInstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(null);
        $instruction->getCodeLength()->willReturn(1);

        $this->shouldThrow(\InvalidArgumentException::class)->during('isGenerationPossible', [$instruction]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getPossibleGenerationAmount', [$instruction]);
    }

    function it_throws_an_invalid_argument_exception_when_expecte_code_length_is_null(
        PromotionCouponGeneratorInstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(18);
        $instruction->getCodeLength()->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('isGenerationPossible', [$instruction]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getPossibleGenerationAmount', [$instruction]);
    }
}
