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

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UpdateHandlingExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UpdateHandlingException::class);
    }

    function it_extends_an_exception()
    {
        $this->shouldHaveType(\Exception::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn('Ups, something went wrong, please try again.');
    }

    function it_has_a_flash()
    {
        $this->getFlash()->shouldReturn('something_went_wrong_error');
    }

    function it_has_an_api_response_code()
    {
        $this->getApiResponseCode()->shouldReturn(400);
    }
}
