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
use Sylius\Component\Resource\Exception\UpdateHandlingException;

final class RaceConditionExceptionSpec extends ObjectBehavior
{
    function it_extends_an_update_handling_exception(): void
    {
        $this->shouldHaveType(UpdateHandlingException::class);
    }

    function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Operated entity was previously modified.');
    }

    function it_has_a_flash(): void
    {
        $this->getFlash()->shouldReturn('race_condition_error');
    }

    function it_has_an_api_response_code(): void
    {
        $this->getApiResponseCode()->shouldReturn(409);
    }
}
