<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CouponSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Model\Coupon');
    }

    function it_should_be_Sylius_coupon()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Model\CouponInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_should_be_mutable()
    {
        $this->setCode('xxx');
        $this->getCode()->shouldReturn('xxx');
    }

    function it_should_not_have_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function it_should_have_no_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_should_be_mutable()
    {
        $this->setUsageLimit(10);
        $this->getUsageLimit()->shouldReturn(10);
    }

    function it_should_not_be_used_by_default()
    {
        $this->getUsed()->shouldReturn(0);
    }

    function its_used_should_be_mutable()
    {
        $this->setUsed(5);
        $this->getUsed()->shouldReturn(5);
    }

    function its_used_should_be_incrementable()
    {
        $this->incrementUsed();
        $this->getUsed()->shouldReturn(1);
    }

    function it_should_not_have_promotion_by_default()
    {
        $this->getPromotion()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     */
    function its_promotion_by_should_be_mutable($promotion)
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }
}
