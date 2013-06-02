<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Processor;

use PHPSpec2\ObjectBehavior;

/**
 * Promotion processor spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessor extends ObjectBehavior
{
    /**
     * @param \Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface      $repository
     * @param \Sylius\Bundle\PromotionsBundle\Checker\PromotionEligibilityCheckerInterface $checker
     * @param \Sylius\Bundle\PromotionsBundle\Action\PromotionApplicatorInterface          $applicator
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
     * @param \Sylius\Bundle\PromotionsBundle\Model\PromotionInterface        $promotion
     * @param \Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_not_apply_promotions_that_are_not_eligible($repository, $checker, $applicator, $subject, $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    /**
     * @param \Sylius\Bundle\PromotionsBundle\Model\PromotionInterface        $promotion
     * @param \Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface $subject
     */
    function it_should_apply_promotions_that_are_eligible($repository, $checker, $applicator, $subject, $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
