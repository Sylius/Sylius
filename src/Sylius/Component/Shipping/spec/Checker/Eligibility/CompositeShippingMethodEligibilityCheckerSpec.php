<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Shipping\Checker\Eligibility;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use stdClass;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class CompositeShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{
    public function let(ShippingMethodEligibilityCheckerInterface $promotionEligibilityChecker): void
    {
        $this->beConstructedWith([$promotionEligibilityChecker]);
    }

    public function it_implements_shipping_method_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ShippingMethodEligibilityCheckerInterface::class);
    }

    public function it_throws_an_exception_if_passed_array_has_not_only_eligibility_checkers(): void
    {
        $this->beConstructedWith([new stdClass()]);

        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_returns_true_if_all_eligibility_checker_returns_true(
        ShippingMethodEligibilityCheckerInterface $firstShippingMethodEligibilityChecker,
        ShippingMethodEligibilityCheckerInterface $secondShippingMethodEligibilityChecker,
        ShippingSubjectInterface $promotionSubject,
        ShippingMethodInterface $promotion
    ): void {
        $this->beConstructedWith([
            $firstShippingMethodEligibilityChecker,
            $secondShippingMethodEligibilityChecker,
        ]);

        $firstShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $secondShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    public function it_returns_false_if_any_eligibility_checker_returns_false(
        ShippingMethodEligibilityCheckerInterface $firstShippingMethodEligibilityChecker,
        ShippingMethodEligibilityCheckerInterface $secondShippingMethodEligibilityChecker,
        ShippingSubjectInterface $promotionSubject,
        ShippingMethodInterface $promotion
    ): void {
        $this->beConstructedWith([
            $firstShippingMethodEligibilityChecker,
            $secondShippingMethodEligibilityChecker,
        ]);

        $firstShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(true);
        $secondShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }

    public function it_stops_checking_at_the_first_failing_eligibility_checker(
        ShippingMethodEligibilityCheckerInterface $firstShippingMethodEligibilityChecker,
        ShippingMethodEligibilityCheckerInterface $secondShippingMethodEligibilityChecker,
        ShippingSubjectInterface $promotionSubject,
        ShippingMethodInterface $promotion
    ): void {
        $this->beConstructedWith([
            $firstShippingMethodEligibilityChecker,
            $secondShippingMethodEligibilityChecker,
        ]);

        $firstShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->willReturn(false);
        $secondShippingMethodEligibilityChecker->isEligible($promotionSubject, $promotion)->shouldNotBeCalled();

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }
}
