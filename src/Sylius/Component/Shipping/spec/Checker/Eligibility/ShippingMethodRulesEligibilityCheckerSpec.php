<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Shipping\Checker\Eligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class ShippingMethodRulesEligibilityCheckerSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $rulesRegistry): void
    {
        $this->beConstructedWith($rulesRegistry);
    }

    public function it_implements_shipping_method_eligibility_checker_interface(): void
    {
        $this->shouldImplement(ShippingMethodEligibilityCheckerInterface::class);
    }

    public function it_recognizes_a_subject_as_eligible_if_a_shipping_method_has_no_rules(ShippingSubjectInterface $subject, ShippingMethodInterface $shippingMethod): void
    {
        $shippingMethod->hasRules()->willReturn(false);
        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    public function it_recognizes_a_subject_as_eligible_if_all_of_shipping_method_rules_are_fulfilled(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        ShippingMethodRuleInterface $firstRule,
        ShippingMethodRuleInterface $secondRule,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $shippingMethod->hasRules()->willReturn(true);
        $shippingMethod->getRules()->willReturn(
            new ArrayCollection([$firstRule->getWrappedObject(), $secondRule->getWrappedObject()]),
        );

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(true);
        $secondRuleChecker->isEligible($subject, [])->willReturn(true);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    public function it_recognizes_a_subject_as_not_eligible_if_any_of_shipping_method_rules_is_not_fulfilled(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        ShippingMethodRuleInterface $firstRule,
        ShippingMethodRuleInterface $secondRule,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $shippingMethod->hasRules()->willReturn(true);
        $shippingMethod->getRules()->willReturn(
            new ArrayCollection([$firstRule->getWrappedObject(), $secondRule->getWrappedObject()]),
        );

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(true);
        $secondRuleChecker->isEligible($subject, [])->willReturn(false);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }

    function it_does_not_check_more_rules_if_one_has_returned_false(
        ServiceRegistryInterface $rulesRegistry,
        RuleCheckerInterface $firstRuleChecker,
        RuleCheckerInterface $secondRuleChecker,
        ShippingMethodRuleInterface $firstRule,
        ShippingMethodRuleInterface $secondRule,
        ShippingMethodInterface $shippingMethod,
        ShippingSubjectInterface $subject,
    ): void {
        $shippingMethod->hasRules()->willReturn(true);
        $shippingMethod->getRules()->willReturn(
            new ArrayCollection([$firstRule->getWrappedObject(), $secondRule->getWrappedObject()]),
        );

        $firstRule->getType()->willReturn('first_rule');
        $firstRule->getConfiguration()->willReturn([]);

        $secondRule->getType()->willReturn('second_rule');
        $secondRule->getConfiguration()->willReturn([]);

        $rulesRegistry->get('first_rule')->willReturn($firstRuleChecker);
        $rulesRegistry->get('second_rule')->willReturn($secondRuleChecker);

        $firstRuleChecker->isEligible($subject, [])->willReturn(false);
        $secondRuleChecker->isEligible($subject, [])->shouldNotBeCalled();

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }
}
