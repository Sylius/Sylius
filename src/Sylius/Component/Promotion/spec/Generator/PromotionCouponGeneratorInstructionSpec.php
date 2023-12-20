<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

final class PromotionCouponGeneratorInstructionSpec extends ObjectBehavior
{
    function it_implements_an_promotion_coupon_genarator_instruction_interface(): void
    {
        $this->shouldImplement(ReadablePromotionCouponGeneratorInstructionInterface::class);
    }

    function it_has_amount_equal_to_5_by_default(): void
    {
        $this->getAmount()->shouldReturn(5);
    }

    function its_amount_should_be_mutable(): void
    {
        $this->beConstructedWith(500);
        $this->getAmount()->shouldReturn(500);
    }

    function its_prefix_is_mutable(): void
    {
        $this->beConstructedWith(null, 'PREFIX_');
        $this->getPrefix()->shouldReturn('PREFIX_');
    }

    function its_code_length_is_mutable(): void
    {
        $this->beConstructedWith(null, null, 4);
        $this->getCodeLength()->shouldReturn(4);
    }

    function its_suffix_is_mutable(): void
    {
        $this->beConstructedWith(null, null, null, '_SUFFIX');
        $this->getSuffix()->shouldReturn('_SUFFIX');
    }

    function it_does_not_have_usage_limit_by_default(): void
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_is_mutable(): void
    {
        $this->beConstructedWith(null, null, null, null, null, 3);
        $this->getUsageLimit()->shouldReturn(3);
    }
}
