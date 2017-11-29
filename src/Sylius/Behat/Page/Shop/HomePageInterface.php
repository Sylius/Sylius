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

use Sylius\Behat\Page\SymfonyPageInterface;

interface HomePageInterface extends SymfonyPageInterface
{
    /**
     * @return string
     */
    public function getContents(): string;

    /**
     * @return bool
     */
    public function hasLogoutButton(): bool;

    public function logOut(): void;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getActiveCurrency(): string;

    /**
     * @return array
     */
    public function getAvailableCurrencies(): array;

    /**
     * @param string $currencyCode
     */
    public function switchCurrency(string $currencyCode): void;

    /**
     * @return string
     */
    public function getActiveLocale(): string;

    /**
     * @return array
     */
    public function getAvailableLocales(): array;

    /**
     * @param string $localeCode
     */
    public function switchLocale(string $localeCode): void;

    /**
     * @return array
     */
    public function getLatestProductsNames(): array;
}
