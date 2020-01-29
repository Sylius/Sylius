<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Dashboard;

class MonthSale
{
    /** @var string */
    private $month;

    /** @var int */
    private $sale;

    public function __construct(string $month, int $sale)
    {
        $this->month = $month;
        $this->sale = $sale;
    }

    public function getMonth(): string
    {
        return $this->month;
    }

    public function getSale(): int
    {
        return $this->sale;
    }
}
