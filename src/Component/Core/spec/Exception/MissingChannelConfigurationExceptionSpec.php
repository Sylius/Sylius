<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Exception;

use PhpSpec\ObjectBehavior;

final class MissingChannelConfigurationExceptionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('Message');
    }

    function it_is_a_runtime_exception(): void
    {
        $this->shouldHaveType(\RuntimeException::class);
    }
}
