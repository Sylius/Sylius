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

class CouponSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Coupon');
    }

    public function it_should_be_Sylius_coupon()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\CouponInterface');
    }

    public function it_should_have_zero_per_user_usage_limit_by_default()
    {
        $this->getPerUserUsageLimit()->shouldReturn(0);
    }

    public function its_per_user_usage_limit_should_be_mutable()
    {
        $this->setPerUserUsageLimit(10);
        $this->getPerUserUsageLimit()->shouldReturn(10);
    }

}
