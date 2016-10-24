<?php

namespace spec\Sylius\Component\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositePromotionEligibilityCheckerSpec extends ObjectBehavior
{
    function let(PromotionEligibilityCheckerInterface $promotionEligibilityChecker)
    {
        $this->beConstructedWith([$promotionEligibilityChecker]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CompositePromotionEligibilityChecker::class);
    }

    function it_is_a_promotion_eligibility_checker()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_all_eligibility_checker_returns_true(
        PromotionEligibilityCheckerInterface $firstPromotionEligibilityChecker,
        PromotionEligibilityCheckerInterface $secondPromotionEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $this->beConstructedWith([
            $firstPromotionEligibilityChecker,
            $secondPromotionEligibilityChecker,
        ]);

        $firstPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $secondPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_returns_false_if_any_eligibility_checker_returns_false(
        PromotionEligibilityCheckerInterface $firstPromotionEligibilityChecker,
        PromotionEligibilityCheckerInterface $secondPromotionEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $this->beConstructedWith([
            $firstPromotionEligibilityChecker,
            $secondPromotionEligibilityChecker,
        ]);

        $firstPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $secondPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    function it_stops_chcecking_at_the_first_failing_eligibility_checker(
        PromotionEligibilityCheckerInterface $firstPromotionEligibilityChecker,
        PromotionEligibilityCheckerInterface $secondPromotionEligibilityChecker,
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion
    ) {
        $this->beConstructedWith([
            $firstPromotionEligibilityChecker,
            $secondPromotionEligibilityChecker,
        ]);

        $firstPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);
        $secondPromotionEligibilityChecker->isEligible($promotionSubject, $promotion)->shouldNotBeCalled();

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
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
