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
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
/**
 * @author Jean-Baptiste Blanchon <jean-baptiste@yproximite.com>
 */
class OrderLoyaltyRuleCheckerSpec extends ObjectBehavior
{

    function let(OrderRepositoryInterface $orderRepository) {
        $this->beConstructedWith($orderRepository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\OrderLoyaltyRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_no_customer_as_not_eligible(OrderInterface $subject)
    {
        $subject->getCustomer()->willReturn(null);

        $this->isEligible($subject, array())->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_customer_is_created_after_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime());

        $this->isEligible($subject, array('nth' => 2, 'unit' => 'months',  'after' => true))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_customer_is_created_before_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject,  array('nth' => 2, 'unit' => 'months', 'after' => true))->shouldReturn(true);
    }

    function it_should_recognize_subject_as_eligible_if_customer_is_created_after_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject,  array('nth' => 2, 'unit' => 'months', 'after' => true))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_customer_is_created_before_configured(
        OrderInterface $subject,
        TimestampableInterface $customer
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getCreatedAt()->willReturn(new \DateTime());

        $this->isEligible($subject,  array('nth' => 2, 'unit' => 'months', 'after' => true))->shouldReturn(true);
    }
}
