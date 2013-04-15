<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Checker;

use PHPSpec2\ObjectBehavior;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

/**
 * Item count rule checker spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleChecker extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Checker\ItemCountRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_recognize_empty_subject_as_not_eligible($subject)
    {
        $subject->getPromotionSubjectItemCount()->shouldBeCalled()->willReturn(0);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_recognize_subject_as_not_eligible_if_item_count_is_less_then_configured($subject)
    {
        $subject->getPromotionSubjectItemCount()->shouldBeCalled()->willReturn(7);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_recognize_subject_as_eligible_if_item_count_is_greater_then_configured($subject)
    {
        $subject->getPromotionSubjectItemCount()->shouldBeCalled()->willReturn(12);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_recognize_subject_as_eligible_if_item_count_is_equal_with_configured_depending_on_equal_setting($subject)
    {
        $subject->getPromotionSubjectItemCount()->shouldBeCalled()->willReturn(10);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
        $this->isEligible($subject, array('count' => 10, 'equal' => true))->shouldReturn(true);
    }

    function it_should_return_item_count_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_promotion_rule_item_count_configuration');
    }
}
