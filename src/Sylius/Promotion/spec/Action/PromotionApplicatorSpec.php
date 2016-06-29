<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Promotion\Action\PromotionActionInterface;
use Sylius\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Promotion\Model\ActionInterface;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Registry\ServiceRegistryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Promotion\Action\PromotionApplicator');
    }

    function it_should_be_Sylius_promotion_applicator()
    {
        $this->shouldImplement(PromotionApplicatorInterface::class);
    }

    function it_should_execute_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        ActionInterface $actionModel
    ) {
        $configuration = [];

        $registry->get('test_action')->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn([$actionModel]);
        $actionModel->getType()->shouldBeCalled()->willReturn('test_action');
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->execute($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_should_revert_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        ActionInterface $actionModel
    ) {
        $configuration = [];

        $registry->get('test_action')->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn([$actionModel]);
        $actionModel->getType()->shouldBeCalled()->willReturn('test_action');
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->revert($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->removePromotion($promotion)->shouldBeCalled();

        $this->revert($subject, $promotion);
    }
}
