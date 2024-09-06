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
use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);
    }

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

    public function specifyFilterChannel(string $channelName): void
    {
        $this->specifyAutocompleteFilter($this->getElement('filter_channel'), $channelName);
    }

    public function specifyFilterShippingMethod(string $methodName): void
    {
        $this->specifyAutocompleteFilter($this->getElement('filter_shipping_method'), $methodName);
    }

    public function chooseFilterCurrency(string $currencyName): void
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
        $this->specifyAutocompleteFilter($this->getElement('filter_product'), $productName);
    }

    public function specifyFilterVariant(string $variantName): void
    {
        $this->specifyAutocompleteFilter($this->getElement('filter_variant'), $variantName);
    }

    public function specifyFilterCustomer(string $customerName): void
    {
        $this->specifyAutocompleteFilter($this->getElement('filter_customer'), $customerName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_channel' => '#criteria_channel',
            'filter_currency' => '#criteria_total_currency',
            'filter_customer' => '#criteria_customer',
            'filter_product' => '#criteria_product',
            'filter_shipping_method' => '#criteria_shipping_method',
            'filter_variant' => '#criteria_variant',
        ]);
    }

    private function specifyAutocompleteFilter(NodeElement $autocomplete, string $value): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $autocomplete->getXpath(),
            $value,
        );

        $this->waitForFormUpdate();
    }
}
