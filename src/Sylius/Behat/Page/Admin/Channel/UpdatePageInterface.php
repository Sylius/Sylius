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

namespace Sylius\Behat\Page\Admin\Channel;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function setTheme(string $themeName): void;

    /**
     * @throws ElementNotFoundException
     */
    public function unsetTheme(): void;

    public function chooseLocale(string $language): void;

    public function chooseCurrency(string $currencyCode): void;

    public function chooseDefaultTaxZone(string $taxZone): void;

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    public function isCodeDisabled(): bool;

    public function isLocaleChosen(string $language): bool;

    public function isCurrencyChosen(string $currencyCode): bool;

    public function isDefaultTaxZoneChosen(string $taxZone): bool;

    public function isAnyDefaultTaxZoneChosen(): bool;

    public function isTaxCalculationStrategyChosen(string $taxCalculationStrategy): bool;

    public function isBaseCurrencyDisabled(): bool;

    public function changeType(string $type): void;

    public function getType(): string;

    public function changeMenuTaxon(string $menuTaxon): void;

    public function getMenuTaxon(): string;

    public function getUsedTheme(): string;
}
