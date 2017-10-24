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

namespace spec\Sylius\Component\Promotion\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;

final class FailedGenerationExceptionSpec extends ObjectBehavior
{
    function let(
        PromotionCouponGeneratorInstructionInterface $instruction,
        \InvalidArgumentException $previousException
    ): void {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $this->beConstructedWith($instruction, 0, $previousException);
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_a_proper_message(): void
    {
        $this
            ->getMessage()
            ->shouldReturn('Invalid coupon code length or coupons amount. It is not possible to generate 17 unique coupons with 1 code length')
        ;
    }

    function it_has_a_proper_previous_exception(\InvalidArgumentException $previousException): void
    {
        $this->getPrevious()->shouldReturn($previousException);
    }
}
