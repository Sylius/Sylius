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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Generator\PromotionCouponGenerator;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponGeneratorSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $promotionCouponFactory,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        ObjectManager $objectManager,
        GenerationPolicyInterface $generationPolicy
    ) {
        $this->beConstructedWith(
            $promotionCouponFactory,
            $promotionCouponRepository,
            $objectManager,
            $generationPolicy
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionCouponGenerator::class);
    }

    function it_implements_a_promotion_coupon_generator_interface()
    {
        $this->shouldImplement(PromotionCouponGeneratorInterface::class);
    }

    function it_generates_coupons_according_to_an_instruction(
        FactoryInterface $promotionCouponFactory,
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        ObjectManager $objectManager,
        PromotionInterface $promotion,
        PromotionCouponInterface $promotionCoupon,
        PromotionCouponGeneratorInstructionInterface $instruction,
        GenerationPolicyInterface $generationPolicy
    ) {
        $instruction->getAmount()->willReturn(1);
        $instruction->getUsageLimit()->willReturn(null);
        $instruction->getExpiresAt()->willReturn(null);
        $instruction->getCodeLength()->willReturn(6);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(true);

        $promotionCouponFactory->createNew()->willReturn($promotionCoupon);
        $promotionCouponRepository->findOneBy(Argument::any())->willReturn(null);
        $promotionCoupon->setPromotion($promotion)->shouldBeCalled();
        $promotionCoupon->setCode(Argument::any())->shouldBeCalled();
        $promotionCoupon->setUsageLimit(null)->shouldBeCalled();
        $promotionCoupon->setExpiresAt(null)->shouldBeCalled();

        $objectManager->persist($promotionCoupon)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->generate($promotion, $instruction);
    }

    function it_throws_a_failed_generation_exception_when_generation_is_not_possible(
        GenerationPolicyInterface $generationPolicy,
        PromotionInterface $promotion,
        PromotionCouponGeneratorInstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(16);
        $instruction->getCodeLength()->willReturn(1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(false);

        $this->shouldThrow(FailedGenerationException::class)->during('generate', [$promotion, $instruction]);
    }

    function it_throws_an_invalid_argument_exception_when_code_length_is_not_between_one_and_forty(
        PromotionCouponInterface $promotionCoupon,
        FactoryInterface $promotionCouponFactory,
        GenerationPolicyInterface $generationPolicy,
        PromotionInterface $promotion,
        PromotionCouponGeneratorInstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(16);
        $instruction->getCodeLength()->willReturn(-1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(true);
        $promotionCouponFactory->createNew()->willReturn($promotionCoupon);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [$promotion, $instruction]);

        $instruction->getCodeLength()->willReturn(45);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [$promotion, $instruction]);
    }
}
