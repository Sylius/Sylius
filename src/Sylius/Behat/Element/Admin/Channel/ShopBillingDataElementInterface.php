<?php

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
