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

namespace Sylius\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class HomePage extends SymfonyPage implements HomePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_homepage';
    }

    public function getContent(): string
    {
        return $this->getDocument()->getContent();
    }

    public function logOut(): void
    {
        $this->getElement('logout_button')->click();
    }

    public function hasLogoutButton(): bool
    {
        return $this->hasElement('logout_button');
    }

    public function getFullName(): string
    {
        if ($this->hasElement('full_name')) {
            return $this->getElement('full_name')->getText();
        }

        return '';
    }

    public function getActiveCurrency(): string
    {
        return $this->getElement('active_currency')->getText();
    }

    public function getAvailableCurrencies(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('currency_selector')->findAll('css', '[data-test-available-currency]'),
        );
    }

    public function switchCurrency(string $currencyCode): void
    {
        try {
            $this->getElement('currency_selector')->click(); // Needed for javascript scenarios
        } catch (UnsupportedDriverActionException) {
        }

        $this->getElement('currency_selector')->clickLink($currencyCode);
    }

    public function getActiveLocale(): string
    {
        return $this->getElement('active_locale')->getAttribute('data-test-active-locale');
    }

    public function getAvailableLocales(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getAttribute('data-test-available-locale'),
            $this->getElement('locale_selector')->findAll('css', '[data-test-available-locale]'),
        );
    }

    public function switchLocale(string $localeCode): void
    {
        $this->getElement('locale_selector')->find('css', sprintf('[data-test-available-locale="%s"]', $localeCode))->click();
    }

    public function getLatestProductsNames(): array
    {
        return $this->getProductsNames('latest_products');
    }

    public function getLatestDealsNames(): array
    {
        return $this->getProductsNames('latest_deals');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'active_currency' => '[data-test-currency-selector] [data-test-active-currency]',
            'active_locale' => '[data-test-locale-selector] [data-test-active-locale]',
            'currency_selector' => '[data-test-currency-selector]',
            'full_name' => '[data-test-full-name]',
            'latest_deals' => '[data-test-latest-deals]',
            'latest_products' => '[data-test-latest-products]',
            'locale_selector' => '[data-test-locale-selector]',
            'logout_button' => '[data-test-button="logout-button"]',
        ]);
    }

    private function getProductsNames(string $elementName): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement($elementName)->findAll('css', '[data-test-product-name]'),
        );
    }
}
