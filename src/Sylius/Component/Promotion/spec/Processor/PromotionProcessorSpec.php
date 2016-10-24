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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionProcessorSpec extends ObjectBehavior
{
    function let(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator
    ) {
        $this->beConstructedWith($preQualifiedPromotionsProvider, $promotionEligibilityChecker, $promotionApplicator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionProcessor::class);
    }

    function it_is_a_promotion_processor()
    {
        $this->shouldImplement(PromotionProcessorInterface::class);
    }

    function it_does_not_apply_promotions_that_are_not_eligible(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->willReturn([]);
        $preQualifiedPromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $promotionEligibilityChecker->isEligible($subject, $promotion)->willReturn(false);

        $promotionApplicator->apply($subject, $promotion)->shouldNotBeCalled();
        $promotionApplicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_applies_promotions_that_are_eligible(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->willReturn([]);
        $preQualifiedPromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $promotionEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);

        $promotionApplicator->apply($subject, $promotion)->shouldBeCalled();
        $promotionApplicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_applies_only_exclusive_promotion(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionInterface $exclusivePromotion
    ) {
        $subject->getPromotions()->willReturn([]);
        $preQualifiedPromotionsProvider->getPromotions($subject)->willReturn([$promotion, $exclusivePromotion]);

        $exclusivePromotion->isExclusive()->willReturn(true);
        $promotionEligibilityChecker->isEligible($subject, $promotion)->willReturn(true);
        $promotionEligibilityChecker->isEligible($subject, $exclusivePromotion)->willReturn(true);

        $promotionApplicator->apply($subject, $exclusivePromotion)->shouldBeCalled();
        $promotionApplicator->apply($subject, $promotion)->shouldNotBeCalled();
        $promotionApplicator->revert($subject, $promotion)->shouldNotBeCalled();
        $promotionApplicator->revert($subject, $exclusivePromotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_reverts_promotions_that_are_not_eligible_anymore(
        PreQualifiedPromotionsProviderInterface $preQualifiedPromotionsProvider,
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    ) {
        $subject->getPromotions()->willReturn([$promotion]);
        $preQualifiedPromotionsProvider->getPromotions($subject)->willReturn([$promotion]);

        $promotionEligibilityChecker->isEligible($subject, $promotion)->willReturn(false);

        $promotionApplicator->apply($subject, $promotion)->shouldNotBeCalled();
        $promotionApplicator->revert($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
