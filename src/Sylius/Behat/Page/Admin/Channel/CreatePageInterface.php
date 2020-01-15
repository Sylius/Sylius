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

    public function nameIt(string $name): void;

    public function specifyCode(string $code): void;

    public function describeItAs(string $description): void;

    public function setHostname(string $hostname): void;

    public function setContactEmail(string $contactEmail): void;

    public function defineColor(string $color): void;

    public function chooseLocale(string $language): void;

    public function chooseCurrency(string $currencyCode): void;

    public function chooseDefaultTaxZone(string $taxZone): void;

    public function chooseDefaultLocale(?string $locale): void;

    public function chooseBaseCurrency(?string $currency): void;

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy): void;

    public function allowToSkipShippingStep(): void;

    public function allowToSkipPaymentStep(): void;

    public function setType(string $type): void;

    public function specifyMenuTaxon(string $menuTaxon): void;
}
