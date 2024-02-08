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
        return $this->getElement('currency_selector')->find('css', '[data-test-active-currency]')->getText();
    }

    public function getAvailableCurrencies(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('currency_selector')->findAll('css', '[data-test-available-currency]'),
        );
    }

    public function switchCurrency($currencyCode): void
    {
        try {
            $this->getElement('currency_selector')->click(); // Needed for javascript scenarios
        } catch (UnsupportedDriverActionException) {
        }

        $this->getElement('currency_selector')->clickLink($currencyCode);
    }

    public function getActiveLocale(): string
    {
        return $this->getElement('locale_selector')->find('css', '[data-test-active-locale]')->getText();
    }

    public function getAvailableLocales(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('locale_selector')->findAll('css', '[data-test-available-locale]'),
        );
    }

    public function switchLocale($localeCode): void
    {
        $this->getElement('locale_selector')->clickLink($localeCode);
    }

    public function getLatestProductsNames(): array
    {
        return array_map(
            fn (NodeElement $element) => $element->getText(),
            $this->getElement('latest_products')->findAll('css', '[data-test-product-name]'),
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'currency_selector' => '[data-test-currency-selector]',
            'full_name' => '[data-test-full-name]',
            'latest_products' => '[data-test-latest-products]',
            'locale_selector' => '[data-test-locale-selector]',
            'logout_button' => '[data-test-logout-button]',
        ]);
    }
}
