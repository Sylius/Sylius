<?php

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
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface                       $repository
     * @param Sylius\Bundle\PromotionsBundle\Checker\PromotionEliglibilityCheckerInterface $checker
     * @param Sylius\Bundle\PromotionsBundle\Action\PromotionApplicatorInterface           $applicator
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
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_not_apply_promotions_that_are_not_eligible($repository, $checker, $applicator, $order, $promotion)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($order, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($order, $promotion)->shouldNotBeCalled();

        $this->process($order);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_should_apply_promotions_that_are_eligible($repository, $checker, $applicator, $order, $promotion)
    {
        $repository->findAll()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($order, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($order, $promotion)->shouldBeCalled();

        $this->process($order);
    }
}
