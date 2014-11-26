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
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NthOrderRuleCheckerSpec extends ObjectBehavior
{
    public function let(OrderRepositoryInterface $ordersRepository)
    {
        $this->beConstructedWith($ordersRepository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\NthOrderRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_no_user_as_not_eligible(OrderInterface $subject)
    {
        $subject->getUser()->willReturn(null);

        $this->isEligible($subject, array('nth' => 10))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_less_then_configured(
        OrderInterface $subject,
        UserInterface $user,
        $ordersRepository
    ) {
        $subject->getUser()->willReturn($user);

        $ordersRepository->countByUserAndPaymentState($user, PaymentInterface::STATE_COMPLETED)->willReturn(5);

        $this->isEligible($subject, array('nth' => 10))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_greater_then_configured(
        OrderInterface $subject,
        UserInterface $user,
        $ordersRepository
    ) {
        $subject->getUser()->willReturn($user);

        $ordersRepository->countByUserAndPaymentState($user, PaymentInterface::STATE_COMPLETED)->willReturn(12);

        $this->isEligible($subject, array('nth' => 10))->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_nth_order_is_equal_with_configured(
        OrderInterface $subject,
        UserInterface $user,
        $ordersRepository
    ) {
        $subject->getUser()->willReturn($user);

        $ordersRepository->countByUserAndPaymentState($user, PaymentInterface::STATE_COMPLETED)->willReturn(9);

        $this->isEligible($subject, array('nth' => 10))->shouldReturn(true);
    }
}
