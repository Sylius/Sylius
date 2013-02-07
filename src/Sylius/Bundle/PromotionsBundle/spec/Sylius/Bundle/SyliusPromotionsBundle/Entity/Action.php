<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Action entity spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Action extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Entity\Action');
    }

    function it_should_be_Sylius_promotion_action()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\ActionInterface');
    }
}
