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

namespace spec\Sylius\Component\Registry;

use PhpSpec\ObjectBehavior;

final class ExistingServiceExceptionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('Service', 'foo');
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_is_an_invalid_argument_exception(): void
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }
}
