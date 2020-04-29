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

final class SalesSummarySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            new \DateTime('01-02-2010'),
            new \DateTime('31-01-2011'),
            ['11.10' => 1200, '12.10' => 400, '01.11' => 500]
        );
    }

    function it_has_intervals_list(): void
    {
        $this->getIntervals()->shouldReturn(
            ['02.10', '03.10', '04.10', '05.10', '06.10', '07.10', '08.10', '09.10', '10.10', '11.10', '12.10', '01.11']
        );
    }

    function it_has_sales_list(): void
    {
        $this->getSales()->shouldReturn(
            ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', '4.00', '5.00']
        );
    }
}
