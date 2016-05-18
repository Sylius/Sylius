<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Generator\InstructionInterface;

/**
 * @mixin FailedGenerationException
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class FailedGenerationExceptionSpec extends ObjectBehavior
{
    function let(InstructionInterface $instruction, \InvalidArgumentException $previousException)
    {
        $instruction->getAmount()->willReturn(17);
        $instruction->getCodeLength()->willReturn(1);
        $this->beConstructedWith($instruction, 0, $previousException);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Exception\FailedGenerationException');
    }

    function it_is_a_exception()
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_proper_message()
    {
        $this->getMessage()->shouldReturn('Invalid coupon code length or coupons amount. It is not possible to generate 17 unique coupons with 1 code length');
    }

    function it_has_proper_previous_exception(\InvalidArgumentException $previousException)
    {
        $this->getPrevious()->shouldReturn($previousException);
    }
}
