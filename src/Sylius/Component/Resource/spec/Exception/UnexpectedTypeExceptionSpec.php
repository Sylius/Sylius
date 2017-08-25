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

namespace spec\Sylius\Component\Resource\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class UnexpectedTypeExceptionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('stringValue', '\ExpectedType');
    }

    function it_extends_invalid_argument_exception(): void
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Expected argument of type "\ExpectedType", "string" given.');
    }
}
