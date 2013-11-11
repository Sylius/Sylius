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
use Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\ShippingBundle\Model\RuleInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingMethodEligibilityCheckerSpec extends ObjectBehavior
{

    function let(RuleCheckerRegistryInterface $registry, RuleCheckerInterface $checker)
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

    function it_returns_true_if_all_checkers_approve_shipping_method($registry, $checker, ShippingSubjectInterface $subject, ShippingMethodInterface $shippingMethod, RuleInterface $rule)
    {
        $configuration = array();

        $rule->getType()->shouldBeCalled()->willReturn(RuleInterface::TYPE_ITEM_TOTAL);
        $rule->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $shippingMethod->getRules()->shouldBeCalled()->willReturn(array($rule));
        $registry->getChecker(RuleInterface::TYPE_ITEM_TOTAL)->shouldBeCalled()->willReturn($checker);

        $checker->isEligible($subject, $configuration)->shouldBeCalled()->willReturn(true);

        $this->isEligible($subject, $shippingMethod)->shouldReturn(true);
    }

    function it_returns_false_if_any_checker_disapproves_shipping_method($registry, $checker, ShippingSubjectInterface $subject, ShippingMethodInterface $shippingMethod, RuleInterface $rule)
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
