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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\AddressFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactory;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class AddressFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_address_with_random_data(): void
    {
        $address = AddressFactory::createOne();

        $this->assertInstanceOf(AddressInterface::class, $address->object());
        $this->assertNotNull($address->getFirstName());
        $this->assertNotNull($address->getLastName());
        $this->assertNotNull($address->getStreet());
        $this->assertNotNull($address->getCity());
        $this->assertNotNull($address->getPostcode());
        $this->assertNotNull($address->getCountryCode());
        $this->assertNotNull($address->getCustomer());
    }

    /** @test */
    function it_creates_address_with_given_first_name(): void
    {
        $address = AddressFactory::new()->withFirstName('Marty')->create();

        $this->assertEquals('Marty', $address->getFirstName());
    }

    /** @test */
    function it_creates_address_with_given_last_name(): void
    {
        $address = AddressFactory::new()->withLastName('McFly')->create();

        $this->assertEquals('McFly', $address->getLastName());
    }

    /** @test */
    function it_creates_address_with_given_phone_number(): void
    {
        $address = AddressFactory::new()->withPhoneNumber('1955-1985-2015')->create();

        $this->assertEquals('1955-1985-2015', $address->getPhoneNumber());
    }

    /** @test */
    function it_creates_address_with_given_company(): void
    {
        $address = AddressFactory::new()->withCompany('Universal Pictures')->create();

        $this->assertEquals('Universal Pictures', $address->getCompany());
    }

    /** @test */
    function it_creates_address_with_given_street(): void
    {
        $address = AddressFactory::new()->withStreet('9303 Lyon Drive, Lyon Estates')->create();

        $this->assertEquals('9303 Lyon Drive, Lyon Estates', $address->getStreet());
    }

    /** @test */
    function it_creates_address_with_given_city(): void
    {
        $address = AddressFactory::new()->withCity('Hill Valley')->create();

        $this->assertEquals('Hill Valley', $address->getCity());
    }

    /** @test */
    function it_creates_address_with_given_post_code(): void
    {
        $address = AddressFactory::new()->withPostcode('95420')->create();

        $this->assertEquals('95420', $address->getPostcode());
    }

    /** @test */
    function it_creates_address_with_given_country_code(): void
    {
        $address = AddressFactory::new()->withCountryCode('US')->create();

        $this->assertEquals('US', $address->getCountryCode());
    }

    /** @test */
    function it_creates_address_with_given_province_name(): void
    {
        $address = AddressFactory::new()->withProvinceName('California')->create();

        $this->assertEquals('California', $address->getProvinceName());
    }

    /** @test */
    function it_creates_address_with_given_province_code(): void
    {
        $address = AddressFactory::new()->withProvinceCode('CA')->create();

        $this->assertEquals('CA', $address->getProvinceCode());
    }

    /** @test */
    function it_creates_address_with_new_given_customer_email(): void
    {
        $address = AddressFactory::new()->withCustomer('marty.mcfly@future.com')->create();

        $this->assertEquals('marty.mcfly@future.com', $address->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_address_with_existing_proxy_given_customer_email(): void
    {
        $shopUser = ShopUserFactory::new()->withEmail('marty.mcfly@future.com')->create();
        $address = AddressFactory::new()->withCustomer($shopUser->getCustomer())->create();

        $this->assertEquals($shopUser->getCustomer(), $address->getCustomer());
    }

    /** @test */
    function it_creates_address_with_existing_given_customer_email(): void
    {
        $shopUser = ShopUserFactory::new()->withEmail('marty.mcfly@future.com')->create()->object();
        $address = AddressFactory::new()->withCustomer($shopUser->getCustomer())->create();

        $this->assertEquals($shopUser->getCustomer(), $address->getCustomer());
    }
}
