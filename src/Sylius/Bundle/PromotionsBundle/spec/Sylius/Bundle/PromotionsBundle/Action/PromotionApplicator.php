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
use Sylius\Bundle\PromotionsBundle\Model\ActionInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionApplicatorSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface $registry
     * @param Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface                  $action
     */
    function let($registry)
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

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface          $subject
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     * @param Sylius\Bundle\PromotionsBundle\Model\ActionInterface    $actionModel
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
