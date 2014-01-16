<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Promotion\Checker;

use PhpSpec\ObjectBehavior;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserLoyalityRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Checker\UserLoyalityRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $subject
     */
    function it_should_recognize_no_user_as_not_eligible($subject)
    {
        $subject->getUser()->shouldBeCalled()->willReturn(null);

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface             $subject
     * @param Sylius\Bundle\ResourceBundle\Model\TimestampableInterface $user
     */
    function it_should_recognize_subject_as_not_eligible_if_user_is_created_after_configured($subject, $user)
    {
        $subject->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime());

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface             $subject
     * @param Sylius\Bundle\ResourceBundle\Model\TimestampableInterface $user
     */
    function it_should_recognize_subject_as_eligible_if_user_is_created_before_configured($subject, $user)
    {
        $subject->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days'))->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface             $subject
     * @param Sylius\Bundle\ResourceBundle\Model\TimestampableInterface $user
     */
    function it_should_recognize_subject_as_eligible_if_user_is_created_after_configured($subject, $user)
    {
        $subject->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime('40 days ago'));

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days', 'after' => true))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface             $subject
     * @param Sylius\Bundle\ResourceBundle\Model\TimestampableInterface $user
     */
    function it_should_recognize_subject_as_not_eligible_if_user_is_created_before_configured($subject, $user)
    {
        $subject->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCreatedAt()->shouldBeCalled()->willReturn(new \DateTime());

        $this->isEligible($subject, array('time' => 30, 'unit' => 'days', 'after' => true))->shouldReturn(true);
    }
}
