<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CustomerLoyaltyRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\CustomerLoyaltyRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_should_recognize_no_customer_as_not_eligible(OrderInterface $subject)
    {
        $subject->getCustomer()->willReturn(null);

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_customer_is_created_after_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime());

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_customer_is_created_before_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(true);
    }

    function it_should_recognize_subject_as_eligible_if_customer_is_created_after_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days', 'after' => true))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_customer_is_created_before_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime());

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days', 'after' => true))->shouldReturn(true);
    }
}
