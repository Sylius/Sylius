<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PromotionCouponGeneratorInstructionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionCouponGeneratorInstruction::class);
    }

    function it_implements_an_promotion_coupon_genarator_instruction_interface()
    {
        $this->shouldImplement(PromotionCouponGeneratorInstructionInterface::class);
    }

    function it_has_amount_equal_to_5_by_default()
    {
        $this->getAmount()->shouldReturn(5);
    }

    function its_amount_should_be_mutable()
    {
        $this->setAmount(500);
        $this->getAmount()->shouldReturn(500);
    }

    function it_does_not_have_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    function its_usage_limit_is_mutable()
    {
        $this->setUsageLimit(3);
        $this->getUsageLimit()->shouldReturn(3);
    }
}
