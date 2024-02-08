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

namespace Sylius\Behat\Page\Admin\Order;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\AutocompleteHelper;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function specifyFilterDateFrom(string $dateTime): void
    {
        $dateAndTime = explode(' ', $dateTime);

        $this->getDocument()->fillField('criteria_date_from_date', $dateAndTime[0]);
        $this->getDocument()->fillField('criteria_date_from_time', $dateAndTime[1] ?? '');
    }

    public function specifyFilterDateTo(string $dateTime): void
    {
        $dateAndTime = explode(' ', $dateTime);

        $this->getDocument()->fillField('criteria_date_to_date', $dateAndTime[0]);
        $this->getDocument()->fillField('criteria_date_to_time', $dateAndTime[1] ?? '');
    }

    public function chooseChannelFilter(string $channelName): void
    {
        $this->getElement('filter_channel')->selectOption($channelName);
    }

    public function chooseShippingMethodFilter(string $methodName): void
    {
        $this->getElement('filter_shipping_method')->selectOption($methodName);
    }

    public function chooseCurrencyFilter(string $currencyName): void
    {
        $this->getElement('filter_currency')->selectOption($currencyName);
    }

    public function specifyFilterTotalGreaterThan(string $total): void
    {
        $this->getDocument()->fillField('criteria_total_greaterThan', $total);
    }

    public function specifyFilterTotalLessThan(string $total): void
    {
        $this->getDocument()->fillField('criteria_total_lessThan', $total);
    }

    public function specifyFilterProduct(string $productName): void
    {
        $productFilterElement = $this->getElement('filter_product')->getParent();

        $this->specifyAutocompleteFilter($productFilterElement, $productName);
    }

    public function specifyFilterVariant(string $variantName): void
    {
        $variantFilterElement = $this->getElement('filter_variant')->getParent();

        $this->specifyAutocompleteFilter($variantFilterElement, $variantName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_channel' => '#criteria_channel',
            'filter_currency' => '#criteria_total_currency',
            'filter_product' => '#criteria_product',
            'filter_shipping_method' => '#criteria_shipping_method',
            'filter_variant' => '#criteria_variant',
            'filters' => '.ui.styled.fluid.accordion:contains("Filters")',
        ]);
    }

    private function specifyAutocompleteFilter(NodeElement $autocomplete, string $value): void
    {
        $this->showFilters();

        AutocompleteHelper::chooseValue($this->getSession(), $autocomplete, $value);
    }

    private function showFilters(): void
    {
        $filters = $this->getElement('filters');
        if ($filters->find('css', '.title')->hasClass('active')) {
            return;
        }

        $filters->click();
    }
}
