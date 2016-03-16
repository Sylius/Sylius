<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\StateMachineCallback;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\CouponInterface;

class CouponUsageCallbackSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\StateMachineCallback\CouponUsageCallback');
    }

    function it_increments_coupon_usage_if_coupon_was_set(
        OrderInterface $order,
        CouponInterface $coupon
    ) {
        $order->getPromotionCoupon()->willReturn($coupon);

        $coupon->incrementUsed()->shouldBeCalled();

        $this->incrementCouponUsage($order);
    }
}
