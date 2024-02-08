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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface HomePageInterface extends SymfonyPageInterface
{
    public function getContent(): string;

    public function hasLogoutButton(): bool;

    public function logOut();

    public function getFullName(): string;

    public function getActiveCurrency(): string;

    public function getAvailableCurrencies(): array;

    public function switchCurrency(string $currencyCode): void;

    public function getActiveLocale(): string;

    public function getAvailableLocales(): array;

    public function switchLocale(string $localeCode): void;

    public function getLatestProductsNames(): array;
}
