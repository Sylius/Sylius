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
use Sylius\Behat\Page\SymfonyPage;

class HomePage extends SymfonyPage implements HomePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        return $this->getDocument()->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function logOut(): void
    {
        $this->getElement('logout_button')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogoutButton(): bool
    {
        return $this->hasElement('logout_button');
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName(): string
    {
        return $this->getElement('full_name')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveCurrency(): string
    {
        return $this->getElement('currency_selector')->find('css', '.sylius-active-currency')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrencies(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('currency_selector')->findAll('css', '.sylius-available-currency')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function switchCurrency(string $currencyCode): void
    {
        try {
            $this->getElement('currency_selector')->click(); // Needed for javascript scenarios
        } catch (UnsupportedDriverActionException $exception) {
        }

        $this->getElement('currency_selector')->clickLink($currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveLocale(): string
    {
        return $this->getElement('locale_selector')->find('css', '.sylius-active-locale')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('locale_selector')->findAll('css', '.sylius-available-locale')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function switchLocale(string $localeCode): void
    {
        $this->getElement('locale_selector')->clickLink($localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestProductsNames(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getDocument()->findAll('css', '.sylius-product-name')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'currency_selector' => '#sylius-currency-selector',
            'locale_selector' => '#sylius-locale-selector',
            'logout_button' => '.sylius-logout-button',
            'full_name' => '.right.menu .item',
        ]);
    }
}
