<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Checker\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Checker\ItemCountRuleChecker');
    }

    function it_is_Sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_should_recognize_empty_subject_as_not_eligible(ShippingSubjectInterface $subject)
    {
        $subject->getShippingItemCount()->shouldBeCalled()->willReturn(0);

        $this->isEligible($subject, ['count' => 10, 'equal' => false])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_item_count_is_less_then_configured(
        ShippingSubjectInterface $subject
    ) {
        $subject->getShippingItemCount()->shouldBeCalled()->willReturn(7);

        $this->isEligible($subject, ['count' => 10, 'equal' => false])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_item_count_is_greater_then_configured(
        ShippingSubjectInterface $subject
    ) {
        $subject->getShippingItemCount()->shouldBeCalled()->willReturn(12);

        $this->isEligible($subject, ['count' => 10, 'equal' => false])->shouldReturn(true);
    }

    function it_should_recognize_subject_as_eligible_if_item_count_is_equal_with_configured_depending_on_equal_setting(
        ShippingSubjectInterface $subject
    ) {
        $subject->getShippingItemCount()->shouldBeCalled()->willReturn(10);

        $this->isEligible($subject, ['count' => 10, 'equal' => false])->shouldReturn(false);
        $this->isEligible($subject, ['count' => 10, 'equal' => true])->shouldReturn(true);
    }

    function it_returns_item_count_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_rule_item_count_configuration');
    }
}
