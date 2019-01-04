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

    public function isCodeDisabled(): bool;

    public function chooseLocale(string $language): void;

    public function isLocaleChosen(string $language): bool;

    public function chooseCurrency(string $currencyCode): void;

    public function isCurrencyChosen(string $currencyCode): bool;

    public function chooseDefaultTaxZone(string $taxZone): void;

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    public function isDefaultTaxZoneChosen(string $taxZone): bool;

    public function isAnyDefaultTaxZoneChosen(): bool;

    public function isTaxCalculationStrategyChosen(string $taxCalculationStrategy): bool;

    public function isBaseCurrencyDisabled(): bool;
}
