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
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CartQuantityRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\CartQuantityRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_should_recognize_empty_subject_as_not_eligible(PromotionCountableSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(0);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_cart_quantity_is_less_then_configured(
        PromotionCountableSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(7);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_cart_quantity_is_greater_then_configured(
        PromotionCountableSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(12);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(true);
    }

    function it_should_recognize_subject_as_eligible_if_cart_quantity_is_equal_with_configured(
        PromotionCountableSubjectInterface $subject
    ) {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(10);

        $this->isEligible($subject, ['count' => 10])->shouldReturn(true);
    }

    function it_should_return_cart_quantity_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_rule_cart_quantity_configuration');
    }
}
