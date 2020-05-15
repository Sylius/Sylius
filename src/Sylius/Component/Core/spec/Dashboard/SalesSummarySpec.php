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

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\Interval;

final class SalesSummarySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            [9 => 1200, 10 => 400, 11 => 500]
        );
    }

    function it_has_intervals_list(): void
    {
        $this->getIntervals()->shouldReturn(
            [9, 10 ,11]
        );
    }

    function it_has_sales_list(): void
    {
        $this->getSales()->shouldReturn(
            [1200, 400, 500]
        );
    }
}
