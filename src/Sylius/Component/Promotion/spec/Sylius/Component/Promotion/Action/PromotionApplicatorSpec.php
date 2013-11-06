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
use Sylius\Component\Promotion\Model\ActionInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicatorSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Component\Promotion\Action\Registry\PromotionActionRegistryInterface $registry
     * @param Sylius\Component\Promotion\Action\PromotionActionInterface                  $action
     */
    function let($registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Action\PromotionApplicator');
    }

    function it_should_be_Sylius_promotion_applicator()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionApplicatorInterface');
    }

    /**
     * @param Sylius\Component\Promotion\Model\PromotionSubjectInterface $subject
     * @param Sylius\Component\Promotion\Model\PromotionInterface        $promotion
     * @param Sylius\Component\Promotion\Model\ActionInterface           $actionModel
     */
    function it_should_execute_all_actions_registered($registry, $action, $subject, $promotion, $actionModel)
    {
        $configuration = array();

        $registry->getAction(ActionInterface::TYPE_FIXED_DISCOUNT)->shouldBeCalled()->willReturn($action);
        $promotion->getActions()->shouldBeCalled()->willReturn(array($actionModel));
        $actionModel->getType()->shouldBeCalled()->willReturn(ActionInterface::TYPE_FIXED_DISCOUNT);
        $actionModel->getConfiguration()->shouldBeCalled()->willReturn($configuration);

        $action->execute($subject, $configuration)->shouldBeCalled();

        $this->apply($subject, $promotion);
    }
}
