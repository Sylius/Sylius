<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface;
use Sylius\Bundle\PromotionsBundle\Model\ActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicatorSpec extends ObjectBehavior
{
    function let(PromotionActionRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Action\PromotionApplicator');
    }

    function it_should_be_Sylius_promotion_applicator()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Action\PromotionApplicatorInterface');
    }

    function it_should_execute_all_actions_registered(
            PromotionActionRegistryInterface $registry,
            PromotionActionInterface $action,
            PromotionSubjectInterface $subject,
            PromotionInterface $promotion,
            ActionInterface $actionModel)
    {
        $configuration = array();

        $registry->getAction(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn(array($actionModel));
        $actionModel->getType()->shouldBeCalled()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->execute($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_should_revert_all_actions_registered(
            PromotionActionRegistryInterface $registry,
            PromotionActionInterface $action,
            PromotionSubjectInterface $subject,
            PromotionInterface $promotion,
            ActionInterface $actionModel)
    {
        $configuration = array();

        $registry->getAction(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn(array($actionModel));
        $actionModel->getType()->shouldBeCalled()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->revert($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->removePromotion($promotion)->shouldBeCalled();

        $this->revert($subject, $promotion);
    }
}
