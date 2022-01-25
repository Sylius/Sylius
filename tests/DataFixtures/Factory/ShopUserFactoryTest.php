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
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShopUserFactoryTest extends KernelTestCase
{
    use Factories;

    /** @test */
    function it_creates_customers(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
    }

    /** @test */
    function it_creates_customers_with_emails(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withEmail('shop@sylius.com')->create();

        $this->assertEquals('shop@sylius.com', $shopUser->getCustomer()->getEmail());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_customers_with_first_names(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withFirstName('Marty')->create();

        $this->assertEquals('Marty', $shopUser->getCustomer()->getFirstName());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getFirstName());
    }

    /** @test */
    function it_creates_customers_with_last_names(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withLastName('McFly')->create();

        $this->assertEquals('McFly', $shopUser->getCustomer()->getLastName());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getLastName());
    }

    /** @test */
    function it_creates_male_customers(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->male()->create();

        $this->assertEquals('m', $shopUser->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_female_customers(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->female()->create();

        $this->assertEquals('f', $shopUser->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_customers_with_phone_numbers(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withPhoneNumber('0102030405')->create();

        $this->assertEquals('0102030405', $shopUser->getCustomer()->getPhoneNumber());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getPhoneNumber());
    }

    /** @test */
    function it_creates_customers_with_birthdays(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $shopUser = ShopUserFactory::new()->withoutPersisting()->withBirthday($birthday)->create();

        $this->assertEquals($birthday, $shopUser->getCustomer()->getBirthday());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->withBirthday('39 years ago')->create();

        $this->assertEquals($birthday->format('Y-m-d'), $shopUser->getCustomer()->getBirthday()->format('Y-m-d'));

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getBirthday());
    }

    /** @test */
    function it_creates_customers_with_password(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withPassword('passw0rd')->create();

        $this->assertEquals('passw0rd', $shopUser->getPlainPassword());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getPlainPassword());
    }

    /** @test */
    function it_creates_customers_with_groups(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($shopUser->getCustomer()->getGroup());

        $shopUser = ShopUserFactory::new()->withoutPersisting()->withCustomerGroup('group_a')->create();

        $this->assertEquals('group_a', $shopUser->getCustomer()->getGroup()->getCode());

        $customerGroup = CustomerGroupFactory::new()->withoutPersisting()->withCode('group_a')->create();
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withCustomerGroup($customerGroup)->create();

        $this->assertEquals($customerGroup->object(), $shopUser->getCustomer()->getGroup());

        $customerGroup = CustomerGroupFactory::new()->withoutPersisting()->withCode('group_a')->create()->object();
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withCustomerGroup($customerGroup)->create();

        $this->assertEquals($customerGroup, $shopUser->getCustomer()->getGroup());
    }
}
