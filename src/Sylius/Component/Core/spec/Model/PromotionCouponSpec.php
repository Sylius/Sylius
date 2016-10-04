<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PromotionCoupon;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PromotionCouponSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PromotionCoupon::class);
    }

    public function it_should_be_Sylius_coupon()
    {
        $this->shouldImplement(PromotionCouponInterface::class);
    }

    public function it_should_have_zero_per_customer_usage_limit_by_default()
    {
        $this->getPerCustomerUsageLimit()->shouldReturn(0);
    }

    public function its_per_customer_usage_limit_should_be_mutable()
    {
        $this->setPerCustomerUsageLimit(10);
        $this->getPerCustomerUsageLimit()->shouldReturn(10);
    }
}
