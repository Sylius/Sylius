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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopBillingDataFactory;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShopBillingDataFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shop_billing_data_with_random_data(): void
    {
        $shopBillingData = ShopBillingDataFactory::createOne();

        $this->assertInstanceOf(ShopBillingDataInterface::class, $shopBillingData->object());
        $this->assertNotNull($shopBillingData->getCompany());
        $this->assertNotNull($shopBillingData->getCountryCode());
        $this->assertNotNull($shopBillingData->getStreet());
        $this->assertNotNull($shopBillingData->getCity());
        $this->assertNotNull($shopBillingData->getPostcode());
    }

    /** @test */
    function it_creates_channel_with_given_company(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withCompany('Sylius')->create();

        $this->assertEquals('Sylius', $shopBillingData->getCompany());
    }

    /** @test */
    function it_creates_channel_with_given_tax_id(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withTaxId('1100110011')->create();

        $this->assertEquals('1100110011', $shopBillingData->getTaxId());
    }

    /** @test */
    function it_creates_channel_with_given_country_code(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withCountryCode('FR')->create();

        $this->assertEquals('FR', $shopBillingData->getCountryCode());
    }

    /** @test */
    function it_creates_channel_with_given_street(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withStreet('Blue Street')->create();

        $this->assertEquals('Blue Street', $shopBillingData->getStreet());
    }

    /** @test */
    function it_creates_channel_with_given_city(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withCity('New York')->create();

        $this->assertEquals('New York', $shopBillingData->getCity());
    }

    /** @test */
    function it_creates_channel_with_given_postcode(): void
    {
        $shopBillingData = ShopBillingDataFactory::new()->withPostcode('94111')->create();

        $this->assertEquals('94111', $shopBillingData->getPostcode());
    }
}
