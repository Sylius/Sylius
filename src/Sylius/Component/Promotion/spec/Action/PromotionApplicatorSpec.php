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
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Action\PromotionApplicator;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class PromotionApplicatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionApplicator::class);
    }

    function it_should_be_a_promotion_applicator()
    {
        $this->shouldImplement(PromotionApplicatorInterface::class);
    }

    function it_executes_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $actionModel
    ) {
        $configuration = [];

        $registry->get('test_action')->willReturn($action);
        $promotion->getActions()->willReturn([$actionModel]);
        $actionModel->getType()->willReturn('test_action');
        $actionModel->getConfiguration()->willReturn($configuration);

        $action->execute($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_reverts_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $action,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $actionModel
    ) {
        $configuration = [];

        $registry->get('test_action')->willReturn($action);
        $promotion->getActions()->willReturn([$actionModel]);
        $actionModel->getType()->willReturn('test_action');
        $actionModel->getConfiguration()->willReturn($configuration);

        $action->revert($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->removePromotion($promotion)->shouldBeCalled();

        $this->revert($subject, $promotion);
    }
}
