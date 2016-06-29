<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Core\Model\CustomerInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\PaymentInterface;
use Sylius\Core\Promotion\Checker\NthOrderRuleChecker;
use Sylius\Core\Repository\OrderRepositoryInterface;
use Sylius\Promotion\Checker\RuleCheckerInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

/**
 * @mixin NthOrderRuleChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
        $this->shouldHaveType('Sylius\Core\Promotion\Checker\NthOrderRuleChecker');
    }

    function it_implements_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_subject_without_customer_as_not_eligible(OrderInterface $subject)
    {
        $subject->getCustomer()->willReturn(null);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_not_eligible_if_nth_order_is_zero(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(0);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_not_eligible_if_nth_order_is_less_then_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(5);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_not_eligible_if_nth_order_is_greater_than_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(12);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_eligible_if_nth_order_is_equal_with_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);

        $ordersRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED)->willReturn(9);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_configuration_is_invalid(OrderInterface $subject)
    {
        $this->isEligible($subject, [])->shouldReturn(false);
        $this->isEligible($subject, ['nth' => 'string'])->shouldReturn(false);
    }

    function it_throws_exception_if_subject_is_not_order(PromotionSubjectInterface $subject)
    {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, ['nth' => 10]])
        ;
    }
}
