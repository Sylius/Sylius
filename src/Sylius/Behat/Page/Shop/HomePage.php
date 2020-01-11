<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
        return $this->getElement('full_name')->getText();
    }

    public function getActiveCurrency(): string
    {
        return $this->getElement('currency_selector')->find('css', '.sylius-active-currency')->getText();
    }

    public function getAvailableCurrencies(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('currency_selector')->findAll('css', '.sylius-available-currency')
        );
    }

    public function switchCurrency($currencyCode): void
    {
        try {
            $this->getElement('currency_selector')->click(); // Needed for javascript scenarios
        } catch (UnsupportedDriverActionException $exception) {
        }

        $this->getElement('currency_selector')->clickLink($currencyCode);
    }

    public function getActiveLocale(): string
    {
        return $this->getElement('locale_selector')->find('css', '.sylius-active-locale')->getText();
    }

    public function getAvailableLocales(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('locale_selector')->findAll('css', '.sylius-available-locale')
        );
    }

    public function switchLocale($localeCode): void
    {
        $this->getElement('locale_selector')->clickLink($localeCode);
    }

    public function getLatestProductsNames(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('latest_products')->findAll('css', '.sylius-product-name')
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'currency_selector' => '#sylius-currency-selector',
            'full_name' => '.right.menu .item',
            'latest_products' => '[data-test-latest-products]',
            'locale_selector' => '#sylius-locale-selector',
            'logout_button' => '[data-test-logout-button]',
        ]);
    }
}
