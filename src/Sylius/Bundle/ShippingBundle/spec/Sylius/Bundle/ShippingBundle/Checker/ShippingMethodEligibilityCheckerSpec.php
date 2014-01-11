<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\RuleInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface $registry
     * @param Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface                  $checker
     */
    function let($registry, $checker)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEligibilityChecker');
    }

    function it_implements_Sylius_shipping_method_eligibility_checker_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEligibilityCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface  $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface   $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\RuleInterface             $rule
     */
    function it_returns_true_if_all_checkers_approve_shipping_method($registry, $checker, $subject, $shippingMethod, $rule)
    {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $shippingMethod->getCategory()->shouldBeCalled();
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\RuleInterface            $rule
     */
    function it_returns_false_if_any_checker_disapproves_shipping_method($registry, $checker, $subject, $shippingMethod, $rule)
    {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $shippingMethod->getCategory()->shouldBeCalled();
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface  $shippingCategory
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface  $shippable
     */
    function it_approves_category_requirement_if_categories_match($subject, $shippingMethod, $shippingCategory, $shippable)
    {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->shouldBeCalled()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $subject->getShippables()->shouldBeCalled()->willReturn(array($shippable));

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface  $shippingCategory
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface  $shippable
     */
    function it_approves_category_requirement_if_no_category_is_required($subject, $shippingMethod)
    {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn(null);

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface  $shippingCategory
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface  $shippingCategory2
     * @param Sylius\Bundle\ShippingBundle\Model\ShippableInterface  $shippable
     */
    function it_denies_category_requirement_if_categories_dont_match($subject, $shippingMethod, $shippingCategory, $shippingCategory2, $shippable)
    {
        $shippingMethod->getCategory()->shouldBeCalled()->willReturn($shippingCategory);
        $shippingMethod->getCategoryRequirement()->shouldBeCalled()->willReturn(ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY);

        $shippable->getShippingCategory()->shouldBeCalled()->willReturn($shippingCategory2);
        $subject->getShippables()->shouldBeCalled()->willReturn(array($shippable));

        $this->isCategoryEligible($subject, $shippingMethod)->shouldReturn(false);
    }
}
