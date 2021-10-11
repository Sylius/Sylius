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

namespace spec\Sylius\Component\Promotion\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;

final class CalendarSpec extends ObjectBehavior
{
    function it_implements_a_date_time_provider(): void
    {
        $this->shouldImplement(DateTimeProviderInterface::class);
    }

    function it_provides_a_date(): void
    {
        $this->now()->shouldBeAnInstanceOf(\DateTimeInterface::class);
    }
}
