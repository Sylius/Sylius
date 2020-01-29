<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;

final class MonthSaleSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('09.12', 1200);
    }

    function it_has_a_month(): void
    {
        $this->getMonth()->shouldReturn('09.12');
    }

    function it_has_a_sale(): void
    {
        $this->getSale()->shouldReturn(1200);
    }
}
