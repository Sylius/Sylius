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
use Sylius\Component\Promotion\Checker\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Checker\PromotionSubjectEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RulesEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $rulesRegistry)
    {
        $this->beConstructedWith($rulesRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Checker\RulesEligibilityChecker');
    }

    function it_implements_promotion_subject_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionSubjectEligibilityCheckerInterface::class);
    }

    function it_recognizes_subject_as_eligible_if_promotion_has_no_rules(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $promotion->hasRules()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_subject_as_eligible_if_all_of_promotion_rules_are_fulfilled(
        RuleCheckerInterface $cartQuantityRuleChecker,
        RuleCheckerInterface $itemTotalRuleChecker,
        RuleInterface $cartQuantityRule,
        RuleInterface $itemTotalRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
        ServiceRegistryInterface $rulesRegistry
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$cartQuantityRule, $itemTotalRule]);

        $cartQuantityRule->getType()->willReturn(ItemTotalRuleChecker::TYPE);
        $cartQuantityRule->getConfiguration()->willReturn([]);

        $itemTotalRule->getType()->willReturn(ItemTotalRuleChecker::TYPE);
        $itemTotalRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get(ItemTotalRuleChecker::TYPE)->willReturn($cartQuantityRuleChecker);
        $rulesRegistry->get(ItemTotalRuleChecker::TYPE)->willReturn($itemTotalRuleChecker);

        $cartQuantityRuleChecker->isEligible($subject, [])->willReturn(true);
        $itemTotalRuleChecker->isEligible($subject, [])->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_any_of_promotion_rules_is_not_fulfilled(
        RuleCheckerInterface $cartQuantityRuleChecker,
        RuleCheckerInterface $itemTotalRuleChecker,
        RuleInterface $cartQuantityRule,
        RuleInterface $itemTotalRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
        ServiceRegistryInterface $rulesRegistry
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$cartQuantityRule, $itemTotalRule]);

        $cartQuantityRule->getType()->willReturn(ItemTotalRuleChecker::TYPE);
        $cartQuantityRule->getConfiguration()->willReturn([]);

        $itemTotalRule->getType()->willReturn(ItemTotalRuleChecker::TYPE);
        $itemTotalRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get(ItemTotalRuleChecker::TYPE)->willReturn($cartQuantityRuleChecker);
        $rulesRegistry->get(ItemTotalRuleChecker::TYPE)->willReturn($itemTotalRuleChecker);

        $cartQuantityRuleChecker->isEligible($subject, [])->willReturn(true);
        $itemTotalRuleChecker->isEligible($subject, [])->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_does_not_check_more_rules_if_one_has_returned_false(
        RuleCheckerInterface $cartQuantityRuleChecker,
        RuleCheckerInterface $itemTotalRuleChecker,
        RuleInterface $cartQuantityRule,
        RuleInterface $itemTotalRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject,
        ServiceRegistryInterface $rulesRegistry
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$cartQuantityRule, $itemTotalRule]);

        $cartQuantityRule->getType()->willReturn(CartQuantityRuleChecker::TYPE);
        $cartQuantityRule->getConfiguration()->willReturn([]);

        $itemTotalRule->getType()->willReturn(ItemTotalRuleChecker::TYPE);
        $itemTotalRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get(CartQuantityRuleChecker::TYPE)->willReturn($cartQuantityRuleChecker);
        $rulesRegistry->get(ItemTotalRuleChecker::TYPE)->willReturn($itemTotalRuleChecker);

        $cartQuantityRuleChecker->isEligible($subject, [])->willReturn(false);
        $itemTotalRuleChecker->isEligible($subject, [])->shouldNotBeCalled();

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
