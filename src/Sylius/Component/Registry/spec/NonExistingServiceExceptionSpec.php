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

final class NonExistingServiceExceptionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('renderer', 'foo', ['service1', 'service2']);
    }

    function it_is_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_is_an_invalid_argument_exception(): void
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    function it_should_show_the_context_and_available_services_in_the_message(): void
    {
        $this->getMessage()->shouldReturn('Renderer service "foo" does not exist, available renderer services: "service1", "service2"');
    }
}
