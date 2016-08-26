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
use Sylius\Component\Core\Promotion\Checker\NthOrderRuleChecker;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @mixin NthOrderRuleChecker
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class NthOrderRuleCheckerSpec extends ObjectBehavior
{
    public function let(OrderRepositoryInterface $ordersRepository)
    {
        $this->beConstructedWith($ordersRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\NthOrderRuleChecker');
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
        $customer->getId()->willReturn(1);

        $ordersRepository->countByCustomer($customer)->willReturn(0);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_not_eligible_if_nth_order_is_less_then_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $ordersRepository->countByCustomer($customer)->willReturn(5);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_not_eligible_if_nth_order_is_greater_than_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $ordersRepository->countByCustomer($customer)->willReturn(12);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
    }

    function it_recognizes_subject_as_eligible_if_nth_order_is_equal_with_configured(
        CustomerInterface $customer,
        OrderInterface $subject,
        OrderRepositoryInterface $ordersRepository
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $ordersRepository->countByCustomer($customer)->willReturn(9);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(true);
    }

    function it_recognizes_subject_as_eligible_if_nth_order_is_one_and_customer_is_not_in_database(
        CustomerInterface $customer,
        OrderInterface $subject
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(null);


        $this->isEligible($subject, ['nth' => 1])->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_it_is_first_order_of_new_customer_and_promotion_is_for_more_than_one_order(
        CustomerInterface $customer,
        OrderInterface $subject
    ) {
        $subject->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(null);

        $this->isEligible($subject, ['nth' => 10])->shouldReturn(false);
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
