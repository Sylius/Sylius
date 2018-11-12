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
    public function getContents(): string;

    public function hasLogoutButton(): bool;

    public function logOut();

    public function getFullName(): string;

    public function getActiveCurrency(): string;

    public function getAvailableCurrencies(): array;

    public function switchCurrency(string $currencyCode);

    public function getActiveLocale(): string;

    public function getAvailableLocales(): array;

    public function switchLocale(string $localeCode);

    public function getLatestProductsNames(): array;
}
