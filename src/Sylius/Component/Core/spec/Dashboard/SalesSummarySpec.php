<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;

final class SalesSummarySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            '11.10' => 1200,
            '12.10' => 400,
            '01.11' => 500,
        ]);
    }

    function it_has_months_list(): void
    {
        $this->getMonths()->shouldReturn(['11.10', '12.10', '01.11']);
    }

    function it_has_sales_list(): void
    {
        $this->getSales()->shouldReturn(['12.00', '4.00', '5.00']);
    }
}
