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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\RuleCheckerInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\RuleInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Checker\ShippingMethodEligibilityChecker');
    }

    function it_implements_Sylius_shipping_method_eligibility_checker_interface()
    {
        $this->shouldImplement(ShippingMethodEligibilityCheckerInterface::class);
    }

    function it_returns_true_if_all_checkers_approve_shipping_method(
        $registry,
        RuleCheckerInterface $checker,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        RuleInterface $rule
    ) {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $shippingMethod->getCategory()->shouldBeCalled();
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_returns_false_if_any_checker_disapproves_shipping_method(
        $registry,
        RuleCheckerInterface $checker,
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        RuleInterface $rule
    ) {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $shippingMethod->getCategory()->shouldBeCalled();
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->get(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }

    function it_approves_category_requirement_if_categories_match(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
        ShippableInterface $shippable
    ) {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->shouldBeCalled()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $subject->getShippables()->shouldBeCalled()->willReturn(array($shippable));

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_approves_category_requirement_if_no_category_is_required(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod
    ) {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn(null);

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_denies_category_requirement_if_categories_dont_match(
        ShippingSubjectInterface $subject,
        ShippingMethodInterface $shippingMethod,
        ShippingCategoryInterface $shippingCategory,
        ShippingCategoryInterface $shippingCategory2,
        ShippableInterface $shippable
    ) {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->shouldBeCalled()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->shouldBeCalled()->willReturn($shippingCategory2);
        $subject->getShippables()->shouldBeCalled()->willReturn(array($shippable));

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(false);
    }
}
