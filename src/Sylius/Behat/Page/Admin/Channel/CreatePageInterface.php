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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable();

    public function disable();

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $description
     */
    public function describeItAs($description);

    /**
     * @param string $hostname
     */
    public function setHostname($hostname);

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail);

    /**
     * @param string $color
     */
    public function defineColor($color);

    /**
     * @param string $language
     */
    public function chooseLocale($language);

    /**
     * @param string $currencyCode
     */
    public function chooseCurrency($currencyCode);

    /**
     * @param string $taxZone
     */
    public function chooseDefaultTaxZone($taxZone);

    /**
     * @param string $locale
     */
    public function chooseDefaultLocale($locale);

    /**
     * @param string $currency
     */
    public function chooseBaseCurrency($currency);

    /**
     * @param string $taxCalculationStrategy
     */
    public function chooseTaxCalculationStrategy($taxCalculationStrategy);

    public function allowToSkipShippingStep();

    public function allowToSkipPaymentStep();
}
