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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactory;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class CustomerFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_with__default_values(): void
    {
        $customer = CustomerFactory::createOne();

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertNotNull($customer->getEmail());
        $this->assertNotNull($customer->getFirstName());
        $this->assertNotNull($customer->getLastName());
        $this->assertNotNull($customer->getPhoneNumber());
        $this->assertNotNull($customer->getBirthday());
        $this->assertNotNull($customer->getGroup());
    }

    /** @test */
    function it_creates_customer_with_given_email(): void
    {
        $customer = CustomerFactory::new()->withEmail('shop@sylius.com')->create();

        $this->assertEquals('shop@sylius.com', $customer->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        $customer = CustomerFactory::new()->withFirstName('Marty')->create();

        $this->assertEquals('Marty', $customer->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        $customer = CustomerFactory::new()->withLastName('McFly')->create();

        $this->assertEquals('McFly', $customer->getLastName());
    }

    /** @test */
    function it_creates_male_customer(): void
    {
        $customer = CustomerFactory::new()->male()->create();

        $this->assertEquals('m', $customer->getGender());
    }

    /** @test */
    function it_creates_female_customer(): void
    {
        $customer = CustomerFactory::new()->female()->create();

        $this->assertEquals('f', $customer->getGender());
    }

    /** @test */
    function it_creates_customer_with_given_phone_number(): void
    {
        $customer = CustomerFactory::new()->withPhoneNumber('0102030405')->create();

        $this->assertEquals('0102030405', $customer->getPhoneNumber());
    }

    /** @test */
    function it_creates_customer_with_given_birthday(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $customer = CustomerFactory::new()->withBirthday($birthday)->create();

        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $customer->getBirthday()->format('Y/m/d H:i:s'));
    }

    /** @test */
    function it_creates_customer_with_given_birthday_via_relative_date(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $customer = CustomerFactory::new()->withBirthday('39 years ago')->create();

        $this->assertEquals($birthday->format('Y-m-d'), $customer->getBirthday()->format('Y-m-d'));
    }

    /** @test */
    function it_creates_customer_with_new_given_group(): void
    {
        $customer = CustomerFactory::new()->withGroup('group_a')->create();

        $this->assertEquals('group_a', $customer->getGroup()->getCode());
    }

    /** @test */
    function it_creates_customer_with_existing_proxy_given_group(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->create();
        $customer = CustomerFactory::new()->withGroup($customerGroup)->create();

        $this->assertEquals($customerGroup->object(), $customer->getGroup());
    }

    /** @test */
    function it_creates_customer_with_existing_given_group(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->create()->object();
        $customer = CustomerFactory::new()->withGroup($customerGroup)->create();

        $this->assertEquals($customerGroup, $customer->getGroup());
    }
}
