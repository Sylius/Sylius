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

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class ShopBillingDataElement extends Element implements ShopBillingDataElementInterface
{
    public function specifyCompany(string $company): void
    {
        $this->getElement('company')->setValue($company);
    }

    public function specifyTaxId(string $taxId): void
    {
        $this->getElement('tax_id')->setValue($taxId);
    }

    public function specifyBillingAddress(string $street, string $postcode, string $city, string $countryCode): void
    {
        $this->getElement('street')->setValue($street);
        $this->getElement('postcode')->setValue($postcode);
        $this->getElement('city')->setValue($city);
        $this->getElement('country_code')->setValue($countryCode);
    }

    public function hasCompany(string $company): bool
    {
        return $company === $this->getElement('company')->getValue();
    }

    public function hasTaxId(string $taxId): bool
    {
        return $taxId === $this->getElement('tax_id')->getValue();
    }

    public function hasBillingAddress(string $street, string $postcode, string $city, string $countryCode): bool
    {
        return
            $street === $this->getElement('street')->getValue() &&
            $postcode === $this->getElement('postcode')->getValue() &&
            $city === $this->getElement('city')->getValue() &&
            $countryCode === $this->getElement('country_code')->getValue()
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'city' => '#sylius_channel_shopBillingData_city',
            'company' => '#sylius_channel_shopBillingData_company',
            'country_code' => '#sylius_channel_shopBillingData_countryCode',
            'postcode' => '#sylius_channel_shopBillingData_postcode',
            'street' => '#sylius_channel_shopBillingData_street',
            'tax_id' => '#sylius_channel_shopBillingData_taxId',
        ]);
    }
}
