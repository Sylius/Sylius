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

namespace spec\Sylius\Component\Core\Exception;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\HandleException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class HandleExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(HandleException::class, 'request does not have locale code');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HandleException::class);
    }

    function it_is_a_runtime_exception()
    {
        $this->shouldHaveType(\RuntimeException::class);
    }

    function it_has_a_message()
    {
        $this->getMessage()->shouldReturn(
            sprintf(
                '%s was unable to handle this request. request does not have locale code',
                HandleException::class
            )
        );
    }
}
