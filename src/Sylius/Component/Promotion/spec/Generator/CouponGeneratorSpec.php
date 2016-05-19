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
use Sylius\Component\Promotion\Generator\CouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\InstructionInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\CouponRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGeneratorSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $couponFactory,
        CouponRepositoryInterface $couponRepository,
        ObjectManager $objectManager,
        GenerationPolicyInterface $generationPolicy
    ) {
        $this->beConstructedWith($couponFactory, $couponRepository, $objectManager, $generationPolicy);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Generator\CouponGenerator');
    }

    function it_should_implement_Sylius_promotion_coupon_generator_interface()
    {
        $this->shouldImplement(CouponGeneratorInterface::class);
    }

    function it_should_generate_coupons_according_to_instruction(
        FactoryInterface $couponFactory,
        CouponRepositoryInterface $couponRepository,
        ObjectManager $objectManager,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        InstructionInterface $instruction,
        GenerationPolicyInterface $generationPolicy
    ) {
        $instruction->getAmount()->willReturn(1);
        $instruction->getUsageLimit()->willReturn(null);
        $instruction->getExpiresAt()->willReturn(null);
        $instruction->getCodeLength()->willReturn(6);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(true);

        $couponFactory->createNew()->willReturn($coupon);
        $couponRepository->findOneBy(Argument::any())->willReturn(null);
        $coupon->setPromotion($promotion)->shouldBeCalled();
        $coupon->setCode(Argument::any())->shouldBeCalled();
        $coupon->setUsageLimit(null)->shouldBeCalled();
        $coupon->setExpiresAt(null)->shouldBeCalled();

        $objectManager->persist($coupon)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->generate($promotion, $instruction);
    }

    function it_throws_failed_generation_exception_when_generation_is_not_possible(
        GenerationPolicyInterface $generationPolicy,
        PromotionInterface $promotion,
        InstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(16);
        $instruction->getCodeLength()->willReturn(1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(false);

        $this->shouldThrow(FailedGenerationException::class)->during('generate', [$promotion, $instruction]);
    }

    function it_throws_invalid_argument_exception_when_code_length_is_not_between_one_and_forty(
        CouponInterface $coupon,
        FactoryInterface $couponFactory,
        GenerationPolicyInterface $generationPolicy,
        PromotionInterface $promotion,
        InstructionInterface $instruction
    ) {
        $instruction->getAmount()->willReturn(16);
        $instruction->getCodeLength()->willReturn(-1);
        $generationPolicy->isGenerationPossible($instruction)->willReturn(true);
        $couponFactory->createNew()->willReturn($coupon);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [$promotion, $instruction]);

        $instruction->getCodeLength()->willReturn(45);
        $this->shouldThrow(\InvalidArgumentException::class)->during('generate', [$promotion, $instruction]);
    }
}
