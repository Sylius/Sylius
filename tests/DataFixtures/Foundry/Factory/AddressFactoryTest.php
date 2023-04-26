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

namespace Sylius\Tests\DataFixtures\Foundry\Factory;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\AddressFactory;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class AddressFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_country_with_random_data(): void
    {
        $address = AddressFactory::createOne();

        $this->assertInstanceOf(AddressInterface::class, $address->object());
        $this->assertNotNull($address->getFirstName());
        $this->assertNotNull($address->getLastName());
        $this->assertNotNull($address->getStreet());
        $this->assertNotNull($address->getCity());
        $this->assertNotNull($address->getPostcode());
        $this->assertNotNull($address->getCountryCode());
        // $this->assertNotNull($address->getCustomer());
    }

    /** @test */
    function it_creates_address_with_given_first_name(): void
    {
        $address = AddressFactory::createOne(['firstName' => 'Marty']);

        $this->assertEquals('Marty', $address->getFirstName());
    }

    /** @test */
    function it_creates_address_with_given_last_name(): void
    {
        $address = AddressFactory::createOne(['LastName' => 'McFly']);

        $this->assertEquals('McFly', $address->getLastName());
    }

    /** @test */
    function it_creates_address_with_given_phone_number(): void
    {
        $address = AddressFactory::createOne(['phoneNumber' => '1955-1985-2015']);

        $this->assertEquals('1955-1985-2015', $address->getPhoneNumber());
    }

    /** @test */
    function it_creates_address_with_given_company(): void
    {
        $address = AddressFactory::createOne(['company' => 'Universal Pictures']);

        $this->assertEquals('Universal Pictures', $address->getCompany());
    }

    /** @test */
    function it_creates_address_with_given_street(): void
    {
        $address = AddressFactory::createOne(['street' => '9303 Lyon Drive, Lyon Estates']);

        $this->assertEquals('9303 Lyon Drive, Lyon Estates', $address->getStreet());
    }

    /** @test */
    function it_creates_address_with_given_city(): void
    {
        $address = AddressFactory::createOne(['city' => 'Hill Valley']);

        $this->assertEquals('Hill Valley', $address->getCity());
    }

    /** @test */
    function it_creates_address_with_given_post_code(): void
    {
        $address = AddressFactory::createOne(['postcode' => '95420']);

        $this->assertEquals('95420', $address->getPostcode());
    }

    /** @test */
    function it_creates_address_with_given_country_code(): void
    {
        $address = AddressFactory::createOne(['countryCode' => 'US']);

        $this->assertEquals('US', $address->getCountryCode());
    }

    /** @test */
    function it_creates_address_with_given_province_name(): void
    {
        $address = AddressFactory::createOne(['provinceName' => 'California']);

        $this->assertEquals('California', $address->getProvinceName());
    }

    /** @test */
    function it_creates_address_with_given_province_code(): void
    {
        $address = AddressFactory::createOne(['provinceCode' => 'CA']);

        $this->assertEquals('CA', $address->getProvinceCode());
    }
}
