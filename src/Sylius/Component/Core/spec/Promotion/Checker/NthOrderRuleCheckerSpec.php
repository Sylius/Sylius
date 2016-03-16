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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NthOrderRuleCheckerSpec extends ObjectBehavior
{
    public function let(OrderRepositoryInterface $ordersRepository)
    {
        $this->beConstructedWith($ordersRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\NthOrderRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_should_recognize_no_customer_as_not_eligible(OrderInterface $subject)
    {
        $subject->getCustomer()->willReturn(null);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_zero(
        OrderInterface $subject,
        CustomerInterface $customer,
        $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(0);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_less_then_configured(
        OrderInterface $subject,
        CustomerInterface $customer,
        $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(5);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_greater_then_configured(
        OrderInterface $subject,
        CustomerInterface $customer,
        $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(12);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_equal_with_configured(
        OrderInterface $subject,
        CustomerInterface $customer,
        $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(9);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(true);
    }
}
