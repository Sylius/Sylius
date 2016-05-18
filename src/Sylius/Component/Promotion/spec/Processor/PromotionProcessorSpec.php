<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessorSpec extends ObjectBehavior
{
    function let(
        PreQualifiedPromotionsProviderInterface $activePromotionsProvider,
        PromotionSubjectEligibilityCheckerInterface $checker,
        PromotionApplicatorInterface $applicator
    ) {
        $this->beConstructedWith($activePromotionsProvider, $checker, $applicator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Processor\PromotionProcessor');
    }

    function it_should_be_Sylius_promotion_processor()
    {
        $this->shouldImplement(PromotionProcessorInterface::class);
    }

    function it_should_not_apply_promotions_that_are_not_eligible(
        $activePromotionsProvider,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->shouldBeCalled()->willReturn([]);
        $activePromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_apply_promotions_that_are_eligible(
        $activePromotionsProvider,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->shouldBeCalled()->willReturn([]);
        $activePromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $promotion)->shouldBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_apply_only_exclusive_promotion(
        $activePromotionsProvider,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionInterface $exclusivePromotion
    ) {
        $subject->getPromotions()->shouldBeCalled()->willReturn([]);
        $activePromotionsProvider->getPromotions($subject)->willReturn([$promotion, $exclusivePromotion]);

        $exclusivePromotion->isExclusive()->shouldBeCalled()->willReturn(true);
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $checker->isEligible($subject, $exclusivePromotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $exclusivePromotion)->shouldBeCalled();
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $exclusivePromotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_revert_promotions_that_are_not_eligible_anymore(
        $activePromotionsProvider,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->shouldBeCalled()->willReturn([$promotion]);
        $activePromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);

        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
