<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Processor;

use PhpSpec\ObjectBehavior;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessorSpec extends ObjectBehavior
{
    /**
     * @param \Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface      $repository
     * @param \Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface $checker
     * @param \Sylius\Component\Promotion\Action\PromotionApplicatorInterface          $applicator
     */
    function let($repository, $checker, $applicator)
    {
        $this->beConstructedWith($repository, $checker, $applicator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessor');
    }

    function it_should_be_Sylius_promotion_processor()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessorInterface');
    }

    /**
     * @param \Sylius\Component\Promotion\Model\PromotionInterface        $promotion
     * @param \Sylius\Component\Promotion\Model\PromotionSubjectInterface $subject
     */
    function it_should_not_apply_promotions_that_are_not_eligible($repository, $checker, $applicator, $subject, $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    /**
     * @param \Sylius\Component\Promotion\Model\PromotionInterface        $promotion
     * @param \Sylius\Component\Promotion\Model\PromotionSubjectInterface $subject
     */
    function it_should_apply_promotions_that_are_eligible($repository, $checker, $applicator, $subject, $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
