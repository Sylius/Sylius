<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicatorSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Action\PromotionApplicator');
    }

    public function it_should_be_Sylius_promotion_applicator()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionApplicatorInterface');
    }

    public function it_should_execute_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        ActionInterface $actionModel
    ) {
        $configuration = array();

        $registry->get(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn(array($actionModel));
        $actionModel->getType()->shouldBeCalled()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->execute($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    public function it_should_revert_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        ActionInterface $actionModel
    ) {
        $configuration = array();

        $registry->get(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn(array($actionModel));
        $actionModel->getType()->shouldBeCalled()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->revert($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->removePromotion($promotion)->shouldBeCalled();

        $this->revert($subject, $promotion);
    }
}
