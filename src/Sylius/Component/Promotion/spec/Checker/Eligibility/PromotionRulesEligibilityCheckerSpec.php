<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionRulesEligibilityChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionRulesEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $rulesRegistry)
    {
        $this->beConstructedWith($rulesRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionRulesEligibilityChecker::class);
    }

    function it_implements_a_promotion_eligibility_checker_interface()
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_recognizes_a_subject_as_eligible_if_a_promotion_has_no_rules(
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $promotion->hasRules()->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_all_of_promotion_rules_are_fulfilled(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        PromotionRuleInterface $firstRule,
        PromotionRuleInterface $secondRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$firstRule, $secondRule]);

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(true);
        $secondRuleChecker->isEligible($subject, [])->willReturn(true);

        $this->isEligible($subject, $promotion)->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_not_eligible_if_any_of_promotion_rules_is_not_fulfilled(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        PromotionRuleInterface $firstRule,
        PromotionRuleInterface $secondRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$firstRule, $secondRule]);

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(true);
        $secondRuleChecker->isEligible($subject, [])->willReturn(false);

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }

    function it_does_not_check_more_rules_if_one_has_returned_false(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        PromotionRuleInterface $firstRule,
        PromotionRuleInterface $secondRule,
        PromotionInterface $promotion,
        PromotionSubjectInterface $subject
    ) {
        $promotion->hasRules()->willReturn(true);
        $promotion->getRules()->willReturn([$firstRule, $secondRule]);

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(false);
        $secondRuleChecker->isEligible($subject, [])->shouldNotBeCalled();

        $this->isEligible($subject, $promotion)->shouldReturn(false);
    }
}
