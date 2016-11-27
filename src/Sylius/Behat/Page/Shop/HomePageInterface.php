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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface HomePageInterface extends SymfonyPageInterface
{
    /**
     * @return string
     */
    public function getContents();

    /**
     * @return bool
     */
    public function hasLogoutButton();

    public function logOut();

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @return string
     */
    public function getActiveCurrency();

    /**
     * @return array
     */
    public function getAvailableCurrencies();

    /**
     * @param string $currencyCode
     */
    public function switchCurrency($currencyCode);

    /**
     * @return string
     */
    public function getActiveLocale();

    /**
     * @return array
     */
    public function getAvailableLocales();

    /**
     * @param string $localeCode
     */
    public function switchLocale($localeCode);

    /**
     * @return array
     */
    public function getLatestProductsNames();
}
