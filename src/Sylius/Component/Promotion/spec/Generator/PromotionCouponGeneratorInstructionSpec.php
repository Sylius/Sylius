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

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;

final class PromotionCouponGeneratorInstructionSpec extends ObjectBehavior
{
    function it_implements_an_promotion_coupon_genarator_instruction_interface(): void
    {
        $this->shouldImplement(PromotionCouponGeneratorInstructionInterface::class);
    }

    function it_has_amount_equal_to_5_by_default(): void
    {
        $this->getAmount()->shouldReturn(5);
    }

    function its_amount_should_be_mutable(): void
    {
        $this->setAmount(500);
        $this->getAmount()->shouldReturn(500);
    }

    function it_does_not_have_usage_limit_by_default(): void
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_is_mutable(): void
    {
        $this->setUsageLimit(3);
        $this->getUsageLimit()->shouldReturn(3);
    }
}
