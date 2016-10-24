<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionDurationEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionDurationEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionDurationEligibilityChecker::class);
    }

    function it_implements_a_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_has_not_started_yet(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getStartsAt()->willReturn(new \DateTime('+3 days'));

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_has_already_ended(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getStartsAt()->willReturn(new \DateTime('-5 days'));
        $promotion->getEndsAt()->willReturn(new \DateTime('-3 days'));

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_currently_available(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getStartsAt()->willReturn(new \DateTime('-2 days'));
        $promotion->getEndsAt()->willReturn(new \DateTime('+2 days'));

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_dates_are_not_specified(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }
}
