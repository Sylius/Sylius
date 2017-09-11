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

final class DeleteHandlingExceptionSpec extends ObjectBehavior
{
    function it_extends_an_exception(): void
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Ups, something went wrong during deleting a resource, please try again.');
    }

    function it_has_a_flash(): void
    {
        $this->getFlash()->shouldReturn('something_went_wrong_error');
    }

    function it_has_an_api_response_code(): void
    {
        $this->getApiResponseCode()->shouldReturn(500);
    }
}
