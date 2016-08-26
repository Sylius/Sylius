<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation\Exception;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;

/**
 * @mixin UnsupportedTaxCalculationStrategyException
 *
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class UnsupportedTaxCalculationStrategyExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException');
    }

    function it_extends_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Unsupported tax calculation strategy!');
    }
}
