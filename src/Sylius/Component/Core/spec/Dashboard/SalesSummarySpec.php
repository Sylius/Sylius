<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Dashboard\MonthSale;

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

    function it_contains_months_sale_statistics(): void
    {
        $this->getAll()->shouldGenerate([
            new MonthSale('11.10', 1200),
            new MonthSale('12.10', 400),
            new MonthSale('01.11', 500),
        ]);
    }

    public function getMatchers(): array
    {
        return [
            'generate' => function (\Traversable $expected, array $actual) {
                return iterator_to_array($expected) == $actual;
            }
        ];
    }
}
