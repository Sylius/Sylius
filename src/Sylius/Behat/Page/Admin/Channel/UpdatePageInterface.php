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

    /**
     * @param string $themeName
     */
    public function setTheme(string $themeName): void;

    /**
     * @throws ElementNotFoundException
     */
    public function unsetTheme(): void;

    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $language
     */
    public function chooseLocale(string $language): void;

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isLocaleChosen(string $language): bool;

    /**
     * @param string $currencyCode
     */
    public function chooseCurrency(string $currencyCode): void;

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function isCurrencyChosen(string $currencyCode): bool;

    /**
     * @param string $taxZone
     */
    public function chooseDefaultTaxZone(string $taxZone): void;

    /**
     * @param string $taxCalculationStrategy
     */
    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    /**
     * @param string $taxZone
     *
     * @return bool
     */
    public function isDefaultTaxZoneChosen(string $taxZone): bool;

    /**
     * @return bool
     */
    public function isAnyDefaultTaxZoneChosen(): bool;

    /**
     * @param string $taxCalculationStrategy
     *
     * @return bool
     */
    public function isTaxCalculationStrategyChosen(string $taxCalculationStrategy): bool;

    /**
     * @return bool
     */
    public function isBaseCurrencyDisabled(): bool;
}
