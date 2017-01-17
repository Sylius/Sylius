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
use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionCouponEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositePromotionCouponEligibilityCheckerSpec extends ObjectBehavior
{
    function let(PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker)
    {
        $this->beConstructedWith([$promotionCouponEligibilityChecker]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CompositePromotionCouponEligibilityChecker::class);
    }

    function it_is_a_promotion_eligibility_checker()
    {
        $this->shouldImplement(PromotionCouponEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_all_eligibility_checker_returns_true(
        PromotionCouponEligibilityCheckerInterface $firstPromotionCouponEligibilityChecker,
        PromotionCouponEligibilityCheckerInterface $secondPromotionCouponEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $this->beConstructedWith([
            $firstPromotionCouponEligibilityChecker,
            $secondPromotionCouponEligibilityChecker,
        ]);

        $firstPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(true);
        $secondPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(true);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(true);
    }

    function it_returns_false_if_any_eligibility_checker_returns_false(
        PromotionCouponEligibilityCheckerInterface $firstPromotionCouponEligibilityChecker,
        PromotionCouponEligibilityCheckerInterface $secondPromotionCouponEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $this->beConstructedWith([
            $firstPromotionCouponEligibilityChecker,
            $secondPromotionCouponEligibilityChecker,
        ]);

        $firstPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(true);
        $secondPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(false);

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(false);
    }

    function it_stops_checking_at_the_first_failing_eligibility_checker(
        PromotionCouponEligibilityCheckerInterface $firstPromotionCouponEligibilityChecker,
        PromotionCouponEligibilityCheckerInterface $secondPromotionCouponEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionCouponInterface $promotionCoupon
    ) {
        $this->beConstructedWith([
            $firstPromotionCouponEligibilityChecker,
            $secondPromotionCouponEligibilityChecker,
        ]);

        $firstPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->willReturn(false);
        $secondPromotionCouponEligibilityChecker->isEligible($promotionSubject, $promotionCoupon)->shouldNotBeCalled();

        $this->isEligible($promotionSubject, $promotionCoupon)->shouldReturn(false);
    }

    function it_throws_an_exception_if_no_eligibility_checkers_are_passed()
    {
        $this->beConstructedWith([]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_throws_an_exception_if_passed_array_has_not_only_eligibility_checkers()
    {
        $this->beConstructedWith([new \stdClass()]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
