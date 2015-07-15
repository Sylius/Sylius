<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CouponSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Model\Coupon');
    }

    public function it_should_be_Sylius_coupon()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Model\CouponInterface');
    }

    public function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function its_code_should_be_mutable()
    {
        $this->setCode('xxx');
        $this->getCode()->shouldReturn('xxx');
    }

    public function it_should_not_have_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    public function it_should_have_no_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    public function its_usage_limit_should_be_mutable()
    {
        $this->setUsageLimit(10);
        $this->getUsageLimit()->shouldReturn(10);
    }

    public function it_should_not_be_used_by_default()
    {
        $this->getUsed()->shouldReturn(0);
    }

    public function its_used_should_be_mutable()
    {
        $this->setUsed(5);
        $this->getUsed()->shouldReturn(5);
    }

    public function its_used_should_be_incrementable()
    {
        $this->incrementUsed();
        $this->getUsed()->shouldReturn(1);
    }

    public function it_should_not_have_promotion_by_default()
    {
        $this->getPromotion()->shouldReturn(null);
    }

    public function its_promotion_by_should_be_mutable(PromotionInterface $promotion)
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }
}
