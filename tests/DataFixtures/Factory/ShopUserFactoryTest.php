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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactory;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShopUserFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_with_random_data(): void
    {
        $shopUser = ShopUserFactory::createOne();

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertNotNull($shopUser->getCustomer()->getEmail());
        $this->assertNotNull($shopUser->getCustomer()->getFirstName());
        $this->assertNotNull($shopUser->getCustomer()->getLastName());
        $this->assertNotNull($shopUser->getCustomer()->getPhoneNumber());
        $this->assertNotNull($shopUser->getCustomer()->getBirthday());
        $this->assertNull($shopUser->getPlainPassword());
        $this->assertNotNull($shopUser->getPassword());
        $this->assertNotNull($shopUser->getCustomer()->getGroup());
    }

    /** @test */
    function it_creates_customer_with_given_email(): void
    {
        $shopUser = ShopUserFactory::new()->withEmail('shop@sylius.com')->create();

        $this->assertEquals('shop@sylius.com', $shopUser->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        $shopUser = ShopUserFactory::new()->withFirstName('Marty')->create();

        $this->assertEquals('Marty', $shopUser->getCustomer()->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        $shopUser = ShopUserFactory::new()->withLastName('McFly')->create();

        $this->assertEquals('McFly', $shopUser->getCustomer()->getLastName());
    }

    /** @test */
    function it_creates_male_customer(): void
    {
        $shopUser = ShopUserFactory::new()->male()->create();

        $this->assertEquals('m', $shopUser->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_female_customer(): void
    {
        $shopUser = ShopUserFactory::new()->female()->create();

        $this->assertEquals('f', $shopUser->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_customer_with_given_phone_number(): void
    {
        $shopUser = ShopUserFactory::new()->withPhoneNumber('0102030405')->create();

        $this->assertEquals('0102030405', $shopUser->getCustomer()->getPhoneNumber());
    }

    /** @test */
    function it_creates_customer_with_given_birthday(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $shopUser = ShopUserFactory::new()->withBirthday($birthday)->create();

        $this->assertEquals($birthday, $shopUser->getCustomer()->getBirthday());
    }

    /** @test */
    function it_creates_customer_with_given_birthday_via_relative_date(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $shopUser = ShopUserFactory::new()->withBirthday('39 years ago')->create();

        $this->assertEquals($birthday->format('Y-m-d'), $shopUser->getCustomer()->getBirthday()->format('Y-m-d'));
    }

    /** @test */
    function it_creates_customer_with_given_password(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withPassword('passw0rd')->create();

        $this->assertEquals('passw0rd', $shopUser->getPlainPassword());
    }

    /** @test */
    function it_creates_customer_with_existing_proxy_given_group(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->create();
        $shopUser = ShopUserFactory::new()->withCustomerGroup($customerGroup)->create();

        $this->assertEquals($customerGroup->object(), $shopUser->getCustomer()->getGroup());
    }

    /** @test */
    function it_creates_customer_with_existing_given_group(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->create()->object();
        $shopUser = ShopUserFactory::new()->withCustomerGroup($customerGroup)->create();

        $this->assertEquals($customerGroup, $shopUser->getCustomer()->getGroup());
    }
}
