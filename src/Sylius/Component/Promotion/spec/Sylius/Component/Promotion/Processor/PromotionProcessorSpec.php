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
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessorSpec extends ObjectBehavior
{
    function let(
        PromotionRepositoryInterface $repository,
        PromotionEligibilityCheckerInterface $checker,
        PromotionApplicatorInterface $applicator
    )
    {
        $this->beConstructedWith($repository, $checker, $applicator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Processor\PromotionProcessor');
    }

    function it_should_be_Sylius_promotion_processor()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Processor\PromotionProcessorInterface');
    }

    function it_should_not_apply_promotions_that_are_not_eligible(
        $repository,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    )
    {
        $subject->getPromotions()->shouldBeCalled()->willReturn(array());
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_apply_promotions_that_are_eligible(
        $repository,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    )
    {
        $subject->getPromotions()->shouldBeCalled()->willReturn(array());
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $promotion)->shouldBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_apply_only_exclusive_promotion(
        $repository,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionInterface $exlusivePromotion
    )
    {
        $subject->getPromotions()->shouldBeCalled()->willReturn(array());
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion, $exlusivePromotion));
        $exlusivePromotion->isExclusive()->shouldBeCalled()->willReturn(true);
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $checker->isEligible($subject, $exlusivePromotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $exlusivePromotion)->shouldBeCalled();
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $exlusivePromotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_revert_promotions_that_are_not_eligible_anymore(
        $repository,
        $checker,
        $applicator,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion
    )
    {
        $subject->getPromotions()->shouldBeCalled()->willReturn(array($promotion));
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);

        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
