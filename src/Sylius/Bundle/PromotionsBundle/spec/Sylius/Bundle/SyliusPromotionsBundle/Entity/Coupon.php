<?php

namespace spec\Sylius\Bundle\PromotionsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Coupon entity spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Coupon extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Entity\Coupon');
    }

    function it_should_be_Sylius_coupon()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\CouponInterface');
    }
}
