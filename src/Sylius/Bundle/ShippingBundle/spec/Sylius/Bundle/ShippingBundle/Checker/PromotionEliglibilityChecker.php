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

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\RuleInterface;

/**
 * Shipping method checker spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEliglibilityChecker extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface $registry
     * @param Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface                  $checker
     */
    function let($registry, $checker)
    {
        $this->beConstructedWith($registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEliglibilityChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEliglibilityCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface $shippablesAware
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\RuleInterface            $rule
     */
    function it_should_recognize_subject_as_eligible_if_all_checkers_recognize_it_as_eligible($registry, $checker, $shippablesAware, $shippingMethod, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($shippablesAware, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($shippablesAware, $shippingMethod)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface $shippablesAware
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\RuleInterface            $rule
     */
    function it_should_recognize_subject_as_not_eligible_if_any_checker_recognize_it_as_not_eligible($registry, $checker, $shippablesAware, $shippingMethod, $rule)
    {
        $configuration = array();

        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);
        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $checker->isEligible($shippablesAware, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($shippablesAware, $shippingMethod)->shouldReturn(false);
    }
}
