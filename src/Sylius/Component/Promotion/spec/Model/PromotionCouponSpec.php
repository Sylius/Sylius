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

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class PromotionCouponSpec extends ObjectBehavior
{
    function it_is_a_promotion_coupon(): void
    {
        $this->shouldImplement(PromotionCouponInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('xxx');
        $this->getCode()->shouldReturn('xxx');
    }

    function it_does_not_have_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function it_has_no_usage_limit_by_default(): void
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_is_mutable(): void
    {
        $this->setUsageLimit(10);
        $this->getUsageLimit()->shouldReturn(10);
    }

    function it_does_not_have_used_by_default(): void
    {
        $this->getUsed()->shouldReturn(0);
    }

    function its_used_is_mutable(): void
    {
        $this->setUsed(5);
        $this->getUsed()->shouldReturn(5);
    }

    function its_used_is_incrementable(): void
    {
        $this->incrementUsed();
        $this->getUsed()->shouldReturn(1);
    }

    function its_used_is_decrementable(): void
    {
        $this->setUsed(5);
        $this->decrementUsed();
        $this->getUsed()->shouldReturn(4);
    }

    function it_does_not_have_promotion_by_default(): void
    {
        $this->getPromotion()->shouldReturn(null);
    }

    function its_promotion_by_is_mutable(PromotionInterface $promotion): void
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }
}
