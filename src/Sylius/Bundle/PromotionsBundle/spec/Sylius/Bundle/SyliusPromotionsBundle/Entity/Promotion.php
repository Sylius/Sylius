<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Promotion entity spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Promotion extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Entity\Promotion');
    }

    function it_should_be_Sylius_promotion()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\PromotionInterface');
    }
}
