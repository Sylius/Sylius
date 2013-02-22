<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Rule entity spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Rule extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Entity\Rule');
    }

    function it_should_be_Sylius_promotion_rule()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\RuleInterface');
    }
}
