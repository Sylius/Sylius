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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\ShopBillingDataInterface;

final class ShopBillingDataTest extends TestCase
{
    private ShopBillingData $shopBillingData;

    protected function setUp(): void
    {
        $this->shopBillingData = new ShopBillingData();
    }

    public function testShouldImplementShopBillingDataInterface(): void
    {
        $this->assertInstanceOf(ShopBillingDataInterface::class, $this->shopBillingData);
    }

    public function testShouldCompanyBeMutable(): void
    {
        $this->shopBillingData->setCompany('Ragnarok');

        $this->assertSame('Ragnarok', $this->shopBillingData->getCompany());
    }

    public function testShouldTaxIdBeMutable(): void
    {
        $this->shopBillingData->setTaxId('1100110011');

        $this->assertSame('1100110011', $this->shopBillingData->getTaxId());
    }

    public function testShouldCountryCodeBeMutable(): void
    {
        $this->shopBillingData->setCountryCode('US');

        $this->assertSame('US', $this->shopBillingData->getCountryCode());
    }

    public function testShouldStreetBeMutable(): void
    {
        $this->shopBillingData->setStreet('Blue Street');

        $this->assertSame('Blue Street', $this->shopBillingData->getStreet());
    }

    public function testShouldCityBeMutable(): void
    {
        $this->shopBillingData->setCity('New York');

        $this->assertSame('New York', $this->shopBillingData->getCity());
    }

    public function testShouldPostCodeBeMutable(): void
    {
        $this->shopBillingData->setPostcode('94111');

        $this->assertSame('94111', $this->shopBillingData->getPostcode());
    }
}
