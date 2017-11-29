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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $description
     */
    public function describeItAs(string $description): void;

    /**
     * @param string $hostname
     */
    public function setHostname(string $hostname): void;

    /**
     * @param string $contactEmail
     */
    public function setContactEmail(string $contactEmail): void;

    /**
     * @param string $color
     */
    public function defineColor(string $color): void;

    /**
     * @param string $language
     */
    public function chooseLocale(string $language): void;

    /**
     * @param string $currencyCode
     */
    public function chooseCurrency(string $currencyCode): void;

    /**
     * @param string $taxZone
     */
    public function chooseDefaultTaxZone(string $taxZone): void;

    /**
     * @param string $locale
     */
    public function chooseDefaultLocale(string $locale): void;

    /**
     * @param string $currency
     */
    public function chooseBaseCurrency(string $currency): void;

    /**
     * @param string $taxCalculationStrategy
     */
    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    public function allowToSkipShippingStep(): void;

    public function allowToSkipPaymentStep(): void;
}
