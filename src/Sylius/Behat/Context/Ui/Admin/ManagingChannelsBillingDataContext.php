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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\Channel\ShopBillingDataElementInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsBillingDataContext implements Context
{
    /** @var ShopBillingDataElementInterface */
    private $shopBillingDataElement;

    public function __construct(ShopBillingDataElementInterface $shopBillingDataElement)
    {
        $this->shopBillingDataElement = $shopBillingDataElement;
    }

    /**
     * @When I specify company as :company
     */
    public function specifyCompanyAs(string $company): void
    {
        $this->shopBillingDataElement->specifyCompany($company);
    }

    /**
     * @When I specify tax ID as :taxId
     */
    public function specifyTaxIdAs(string $taxId): void
    {
        $this->shopBillingDataElement->specifyTaxId($taxId);
    }

    /**
     * @When I specify shop billing address as :street, :postcode :city, :country
     */
    public function specifyShopBillingAddressAs(
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        $this->shopBillingDataElement->specifyBillingAddress($street, $postcode, $city, $country->getCode());
    }

    /**
     * @Then this channel company should be :company
     */
    public function thisChannelCompanyShouldBe(string $company): void
    {
        Assert::true($this->shopBillingDataElement->hasCompany($company));
    }

    /**
     * @Then this channel tax ID should be :taxId
     */
    public function thisChanneTaxIdShouldBe(string $taxId): void
    {
        Assert::true($this->shopBillingDataElement->hasTaxId($taxId));
    }

    /**
     * @Then this channel shop billing address should be :street, :postcode :city, :country
     */
    public function thisChannelShopBillingAddressShouldBe(
        string $street,
        string $postcode,
        string $city,
        CountryInterface $country
    ): void {
        Assert::true($this->shopBillingDataElement->hasBillingAddress($street, $postcode, $city, $country->getCode()));
    }
}
