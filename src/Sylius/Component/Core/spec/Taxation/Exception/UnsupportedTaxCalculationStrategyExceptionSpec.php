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

namespace spec\Sylius\Component\Core\Taxation\Exception;

use PhpSpec\ObjectBehavior;

final class UnsupportedTaxCalculationStrategyExceptionSpec extends ObjectBehavior
{
    function it_extends_an_exception(): void
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Unsupported tax calculation strategy!');
    }
}
