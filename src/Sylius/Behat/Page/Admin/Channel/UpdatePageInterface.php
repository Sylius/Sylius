<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Channel;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable();

    public function disable();

    /**
     * @param string $themeName
     */
    public function setTheme($themeName);

    /**
     * @throws ElementNotFoundException
     */
    public function unsetTheme();

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $language
     */
    public function chooseLocale($language);

    /**
     * @param string $language
     *
     * @return bool
     */
    public function isLocaleChosen($language);

    /**
     * @param string $currencyCode
     */
    public function chooseCurrency($currencyCode);

    /**
     * @param string $currencyCode
     *
     * @return bool
     */
    public function isCurrencyChosen($currencyCode);

    /**
     * @param string $taxZone
     */
    public function chooseDefaultTaxZone($taxZone);

    /**
     * @param string $taxCalculationStrategy
     */
    public function chooseTaxCalculationStrategy($taxCalculationStrategy);

    /**
     * @param string $taxZone
     *
     * @return bool
     */
    public function isDefaultTaxZoneChosen($taxZone);

    /**
     * @return bool
     */
    public function isAnyDefaultTaxZoneChosen();

    /**
     * @param string $taxCalculationStrategy
     *
     * @return bool
     */
    public function isTaxCalculationStrategyChosen($taxCalculationStrategy);

    /**
     * @return bool
     */
    public function isBaseCurrencyDisabled();
}
