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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionUsageLimitEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionUsageLimitEligibilityChecker::class);
    }

    function it_implements_a_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_promotion_has_no_usage_limit(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getUsageLimit()->willReturn(null);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_true_if_usage_limit_has_not_been_exceeded(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getUsageLimit()->willReturn(10);
        $promotion->getUsed()->willReturn(5);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_usage_limit_has_been_exceeded(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $promotion->getUsageLimit()->willReturn(10);
        $promotion->getUsed()->willReturn(15);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }
}
