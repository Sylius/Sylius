<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Behaviour\NamesIt;

trait FormAwareTrait
{
    use NamesIt;

    public function chooseZone(string $name): void
    {
        $this->getElement('field_zone')->selectOption($name);
    }

    public function chooseCategory(string $name): void
    {
        $this->getElement('field_category')->selectOption($name);
    }

    public function chooseCalculator(string $name): void
    {
        $this->getElement('field_calculator')->selectOption($name);
    }

    public function specifyAmount(string $amount): void
    {
        $this->getElement('field_amount')->setValue($amount);
    }

    public function specifyStartDate(\DateTimeInterface $startDate): void
    {
        $timestamp = $startDate->getTimestamp();

        $this->getElement('field_start_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('field_start_date_time')->setValue(date('H:i', $timestamp));
    }

    public function specifyEndDate(\DateTimeInterface $endDate): void
    {
        $timestamp = $endDate->getTimestamp();

        $this->getElement('field_end_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('field_end_date_time')->setValue(date('H:i', $timestamp));
    }

    /** @return array<string, string> */
    protected function getDefinedFormElements(): array
    {
        return [
            'field_amount' => '#sylius_tax_rate_amount',
            'field_calculator' => '#sylius_tax_rate_calculator',
            'field_category' => '#sylius_tax_rate_category',
            'field_code' => '#sylius_tax_rate_code',
            'field_end_date' => '#sylius_tax_rate_endDate_date',
            'field_end_date_time' => '#sylius_tax_rate_endDate_time',
            'field_included_in_price' => '#sylius_tax_rate_includedInPrice',
            'field_name' => '#sylius_tax_rate_name',
            'field_start_date' => '#sylius_tax_rate_startDate_date',
            'field_start_date_time' => '#sylius_tax_rate_startDate_time',
            'field_zone' => '#sylius_tax_rate_zone',
        ];
    }
}
