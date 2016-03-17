<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DatesEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\DatesEligibilityChecker');
    }

    function it_implements_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_false_if_promotion_has_not_started_yet(PromotionInterface $promotion)
    {
        $promotion->getStartsAt()->willReturn(new \DateTime('+3 days'));

        $this->isEligible($promotion)->shouldReturn(false);
    }

    function it_returns_false_if_promotion_has_already_ended(PromotionInterface $promotion)
    {
        $promotion->getStartsAt()->willReturn(new \DateTime('-5 days'));
        $promotion->getEndsAt()->willReturn(new \DateTime('-3 days'));

        $this->isEligible($promotion)->shouldReturn(false);
    }

    function it_returns_true_if_promotion_is_currently_available(PromotionInterface $promotion)
    {
        $promotion->getStartsAt()->willReturn(new \DateTime('-2 days'));
        $promotion->getEndsAt()->willReturn(new \DateTime('+2 days'));

        $this->isEligible($promotion)->shouldReturn(true);
    }

    function it_returns_true_if_promotion_dates_are_not_specified(PromotionInterface $promotion)
    {
        $promotion->getStartsAt()->willReturn(null);
        $promotion->getEndsAt()->willReturn(null);

        $this->isEligible($promotion)->shouldReturn(true);
    }
}
