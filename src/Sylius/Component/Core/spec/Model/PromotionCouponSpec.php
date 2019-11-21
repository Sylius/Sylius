<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PromotionCouponSpec extends ObjectBehavior
{
    public function it_is_a_promotion_coupon()
    {
        $this->shouldImplement(PromotionCouponInterface::class);
    }

    public function it_does_have_null_per_customer_usage_limit_by_default()
    {
        $this->getPerCustomerUsageLimit()->shouldReturn(null);
    }

    public function its_per_customer_usage_limit_should_be_mutable()
    {
        $this->setPerCustomerUsageLimit(10);
        $this->getPerCustomerUsageLimit()->shouldReturn(10);
    }

    public function its_reusable_from_cancelled_orders_flag_is_true_by_default(): void
    {
        $this->isReusableFromCancelledOrders()->shouldReturn(true);
    }

    public function its_reusable_from_cancelled_orders_flag_is_mutable(): void
    {
        $this->setReusableFromCancelledOrders(false);
        $this->isReusableFromCancelledOrders()->shouldReturn(false);
    }
}
