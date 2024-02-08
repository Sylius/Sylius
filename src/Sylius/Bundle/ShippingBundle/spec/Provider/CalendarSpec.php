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

namespace spec\Sylius\Bundle\ShippingBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;

final class CalendarSpec extends ObjectBehavior
{
    function it_implements_a_date_time_provider(): void
    {
        $this->shouldImplement(DateTimeProvider::class);
    }

    function it_provides_a_date(): void
    {
        $this->today()->shouldBeAnInstanceOf(\DateTimeImmutable::class);
    }
}
