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
    public function enable();

    public function disable();

    public function nameIt(string $name);

    public function specifyCode(string $code);

    public function describeItAs(string $description);

    public function setHostname(string $hostname);

    public function setContactEmail(string $contactEmail);

    public function defineColor(string $color);

    public function chooseLocale(string $language);

    public function chooseCurrency(string $currencyCode);

    public function chooseDefaultTaxZone(string $taxZone);

    public function chooseDefaultLocale(string $locale);

    public function chooseBaseCurrency(string $currency);

    public function chooseTaxCalculationStrategy(string $taxCalculationStrategy);

    public function allowToSkipShippingStep();

    public function allowToSkipPaymentStep();
}
