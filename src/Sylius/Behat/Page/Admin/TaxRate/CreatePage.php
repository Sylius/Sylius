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

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsField;

    public function chooseZone(string $name): void
    {
        $this->getDocument()->selectFieldOption('Zone', $name);
    }

    public function chooseCategory(string $name): void
    {
        $this->getDocument()->selectFieldOption('Category', $name);
    }

    public function chooseCalculator(string $name): void
    {
        $this->getDocument()->selectFieldOption('Calculator', $name);
    }

    public function specifyAmount(string $amount): void
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    public function specifyStartDate(\DateTimeInterface $startDate): void
    {
        $timestamp = $startDate->getTimestamp();

        $this->getElement('start_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('start_date_time')->setValue(date('H:i', $timestamp));
    }

    public function specifyEndDate(\DateTimeInterface $endDate): void
    {
        $timestamp = $endDate->getTimestamp();

        $this->getElement('end_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('end_date_time')->setValue(date('H:i', $timestamp));
    }

    public function chooseIncludedInPrice(): void
    {
        $this->getDocument()->find('css', 'label[for=sylius_tax_rate_includedInPrice]')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_tax_rate_amount',
            'calculator' => '#sylius_tax_rate_calculator',
            'category' => '#sylius_tax_rate_category',
            'code' => '#sylius_tax_rate_code',
            'end_date' => '#sylius_tax_rate_endDate_date',
            'end_date_time' => '#sylius_tax_rate_endDate_time',
            'name' => '#sylius_tax_rate_name',
            'start_date' => '#sylius_tax_rate_startDate_date',
            'start_date_time' => '#sylius_tax_rate_startDate_time',
            'zone' => '#sylius_tax_rate_zone',
        ]);
    }
}
