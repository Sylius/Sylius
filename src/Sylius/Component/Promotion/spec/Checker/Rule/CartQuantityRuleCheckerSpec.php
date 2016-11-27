<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class CartQuantityRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CartQuantityRuleChecker::class);
    }

    function it_is_a_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_empty_subject_as_not_eligible(CountablePromotionSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->willReturn(0);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_not_eligible_if_a_cart_quantity_is_less_then_configured(
        CountablePromotionSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->willReturn(7);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_eligible_if_a_cart_quantity_is_greater_then_configured(
        CountablePromotionSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->willReturn(12);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_a_cart_quantity_is_equal_with_configured(
        CountablePromotionSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->willReturn(10);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(true);
    }
}
