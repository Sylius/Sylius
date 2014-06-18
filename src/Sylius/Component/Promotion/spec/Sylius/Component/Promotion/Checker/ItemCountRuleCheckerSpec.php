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
use Sylius\Component\Promotion\Model\PromotionCountableSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\ItemCountRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_empty_subject_as_not_eligible(PromotionCountableSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(0);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_item_count_is_less_then_configured(PromotionCountableSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(7);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_item_count_is_greater_then_configured(PromotionCountableSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(12);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(true);
    }

    function it_should_recognize_subject_as_eligible_if_item_count_is_equal_with_configured_depending_on_equal_setting(PromotionCountableSubjectInterface $subject)
    {
        $subject->getPromotionSubjectCount()->shouldBeCalled()->willReturn(10);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
        $this->isEligible($subject, array('count' => 10, 'equal' => true))->shouldReturn(true);
    }

    function it_should_return_item_count_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_rule_item_count_configuration');
    }
}
