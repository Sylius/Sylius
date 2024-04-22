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

namespace Sylius\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function getTheme(): string;

    public function setTheme(string $themeName): void;

    /** @return string[] */
    public function getLocales(): array;

    public function chooseLocale(string $language): void;

    /** @return string[] */
    public function getCurrencies(): array;

    public function chooseCurrency(string $currencyCode): void;

    public function getDefaultTaxZone(): ?string;

    public function chooseDefaultTaxZone(string $taxZone): void;

    public function getTaxCalculationStrategy(): string;

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    public function isCodeDisabled(): bool;

    public function isBaseCurrencyDisabled(): bool;

    public function specifyMenuTaxon(string $menuTaxon): void;

    public function getMenuTaxon(): string;
}
