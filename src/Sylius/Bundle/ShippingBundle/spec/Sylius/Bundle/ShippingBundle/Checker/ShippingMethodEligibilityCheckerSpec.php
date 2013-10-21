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
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjecteInterface $subject
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface   $shippingMethod
     * @param Sylius\Bundle\ShippingBundle\Model\RuleInterface             $rule
     */
    function it_returns_true_if_all_checkers_approve_shipping_method($registry, $checker, $subject, $shippingMethod, $rule)
    {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

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

        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(false);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(false);
    }
}
