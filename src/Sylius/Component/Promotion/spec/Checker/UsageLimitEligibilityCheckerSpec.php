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
class UsageLimitEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\UsageLimitEligibilityChecker');
    }

    function it_implements_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_promotion_has_no_usage_limit(PromotionInterface $promotion)
    {
        $promotion->getUsageLimit()->willReturn(null);

        $this->isEligible($promotion)->shouldReturn(true);
    }

    function it_returns_true_if_usage_limit_has_not_been_exceeded(PromotionInterface $promotion)
    {
        $promotion->getUsageLimit()->willReturn(10);
        $promotion->getUsed()->willReturn(5);

        $this->isEligible($promotion)->shouldReturn(true);
    }

    function it_returns_false_if_usage_limit_has_been_exceeded(PromotionInterface $promotion)
    {
        $promotion->getUsageLimit()->willReturn(10);
        $promotion->getUsed()->willReturn(15);

        $this->isEligible($promotion)->shouldReturn(false);
    }
}
