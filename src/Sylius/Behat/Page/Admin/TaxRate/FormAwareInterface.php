<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\TaxRate;

interface FormAwareInterface
{
    public function nameIt(string $name): void;

    public function specifyAmount(string $amount): void;

    public function specifyStartDate(\DateTimeInterface $startDate): void;

    public function specifyEndDate(\DateTimeInterface $endDate): void;

    public function chooseZone(string $name): void;

    public function chooseCategory(string $name): void;

    public function chooseCalculator(string $name): void;
}
