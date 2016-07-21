<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class HomePage extends SymfonyPage implements HomePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return $this->getDocument()->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogoutButton()
    {
        return $this->hasElement('logout_button');
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveCurrency()
    {
        return $this->getElement('currency_selector')->find('css', '.sylius-active-currency')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrencies()
    {
        return array_map(
            function (NodeElement $element) { return $element->getText(); },
            $this->getElement('currency_selector')->findAll('css', '.sylius-available-currency')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function switchCurrency($currencyCode)
    {
        $this->getElement('currency_selector')->clickLink($currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'currency_selector' => '#sylius-currency-selector',
            'logout_button' => '.sylius-logout-button',
        ]);
    }
}
