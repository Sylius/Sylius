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

namespace Sylius\Bundle\ShippingBundle\spec\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;

final class CalendarSpec extends ObjectBehavior
{
    function it_implements_a_date_time_provider(): void
    {
        $this->shouldImplement(ShippingDateAssignerInterface::class);
    }

    function it_provide_a_date(): void
    {
        $this->today()->shouldReturn(typeOf(new \DateTimeImmutable()));
    }
}
