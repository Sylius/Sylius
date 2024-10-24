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

namespace Sylius\Behat\Element\Admin\Channel;

interface ShopBillingDataElementInterface
{
    public function specifyCompany(string $company): void;

    public function specifyTaxId(string $taxId): void;

    public function specifyBillingAddress(string $street, string $postcode, string $city, string $countryCode): void;

    public function hasCompany(string $company): bool;

    public function hasTaxId(string $taxId): bool;

    public function hasBillingAddress(string $street, string $postcode, string $city, string $countryCode): bool;
}
