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
        PromotionActionCommandInterface $actionCommand,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $action
    ) {
        $configuration = [];

        $registry->get('test_action')->willReturn($actionCommand);
        $promotion->getActions()->willReturn([$action]);
        $action->getType()->willReturn('test_action');
        $action->getConfiguration()->willReturn($configuration);

        $actionCommand->execute($subject, $configuration, $promotion)->willReturn(true);

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_applies_promotion_if_at_least_one_action_was_executed_even_if_the_last_one_was_not(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $firstActionCommand,
        PromotionActionCommandInterface $secondActionCommand,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $firstAction,
        PromotionActionInterface $secondAction
    ) {
        $promotion->getActions()->willReturn([$firstAction, $secondAction]);

        $firstAction->getType()->willReturn('first_action');
        $firstAction->getConfiguration()->willReturn([]);

        $secondAction->getType()->willReturn('second_action');
        $secondAction->getConfiguration()->willReturn([]);

        $registry->get('first_action')->willReturn($firstActionCommand);
        $registry->get('second_action')->willReturn($secondActionCommand);

        $firstActionCommand->execute($subject, [], $promotion)->willReturn(true);
        $secondActionCommand->execute($subject, [], $promotion)->willReturn(false);

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_applies_promotion_if_at_least_one_action_was_executed(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $firstActionCommand,
        PromotionActionCommandInterface $secondActionCommand,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $firstAction,
        PromotionActionInterface $secondAction
    ) {
        $promotion->getActions()->willReturn([$firstAction, $secondAction]);

        $firstAction->getType()->willReturn('first_action');
        $firstAction->getConfiguration()->willReturn([]);

        $secondAction->getType()->willReturn('second_action');
        $secondAction->getConfiguration()->willReturn([]);

        $registry->get('first_action')->willReturn($firstActionCommand);
        $registry->get('second_action')->willReturn($secondActionCommand);

        $firstActionCommand->execute($subject, [], $promotion)->willReturn(false);
        $secondActionCommand->execute($subject, [], $promotion)->willReturn(true);

        $subject->addPromotion($promotion)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_does_not_add_promotion_if_no_action_has_been_applied(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $firstActionCommand,
        PromotionActionCommandInterface $secondActionCommand,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $firstAction,
        PromotionActionInterface $secondAction
    ) {
        $promotion->getActions()->willReturn([$firstAction, $secondAction]);

        $firstAction->getType()->willReturn('first_action');
        $firstAction->getConfiguration()->willReturn([]);

        $secondAction->getType()->willReturn('second_action');
        $secondAction->getConfiguration()->willReturn([]);

        $registry->get('first_action')->willReturn($firstActionCommand);
        $registry->get('second_action')->willReturn($secondActionCommand);

        $firstActionCommand->execute($subject, [], $promotion)->willReturn(false);
        $secondActionCommand->execute($subject, [], $promotion)->willReturn(false);

        $subject->addPromotion($promotion)->shouldNotBeCalled();

        $this->apply($subject, $promotion);
    }

    function it_reverts_all_actions_registered(
        ServiceRegistryInterface $registry,
        PromotionActionCommandInterface $actionCommand,
        PromotionSubjectInterface $subject,
        PromotionInterface $promotion,
        PromotionActionInterface $action
    ) {
        $configuration = [];

        $registry->get('test_action')->willReturn($actionCommand);
        $promotion->getActions()->willReturn([$action]);
        $action->getType()->willReturn('test_action');
        $action->getConfiguration()->willReturn($configuration);

        $actionCommand->revert($subject, $configuration, $promotion)->shouldBeCalled();

        $subject->removePromotion($promotion)->shouldBeCalled();

        $this->revert($subject, $promotion);
    }
}
