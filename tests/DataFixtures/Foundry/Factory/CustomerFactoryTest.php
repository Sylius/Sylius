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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CustomerFactory;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class CustomerFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_with_default_values(): void
    {
        $customer = CustomerFactory::createOne();

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertNotNull($customer->getEmail());
        $this->assertNotNull($customer->getFirstName());
        $this->assertNotNull($customer->getLastName());
        $this->assertNotNull($customer->getPhoneNumber());
        $this->assertNotNull($customer->getBirthday());
        // $this->assertNotNull($customer->getGroup());
    }

    /** @test */
    function it_creates_customer_with_given_email(): void
    {
        $customer = CustomerFactory::createOne(['email' => 'shop@sylius.com']);

        $this->assertEquals('shop@sylius.com', $customer->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        $customer = CustomerFactory::createOne(['firstName' => 'Marty']);

        $this->assertEquals('Marty', $customer->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        $customer = CustomerFactory::createOne(['lastName' => 'McFly']);

        $this->assertEquals('McFly', $customer->getLastName());
    }

    /** @test */
    function it_creates_male_customer(): void
    {
        $customer = CustomerFactory::createOne(['gender' => 'm']);

        $this->assertEquals('m', $customer->getGender());
    }

    /** @test */
    function it_creates_female_customer(): void
    {
        $customer = CustomerFactory::createOne(['gender' => 'f']);

        $this->assertEquals('f', $customer->getGender());
    }

    /** @test */
    function it_creates_customer_with_given_phone_number(): void
    {
        $customer = CustomerFactory::createOne(['phoneNumber' => '0102030405']);

        $this->assertEquals('0102030405', $customer->getPhoneNumber());
    }

    /** @test */
    function it_creates_customer_with_given_birthday(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $customer = CustomerFactory::createOne(['birthday' => $birthday]);

        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $customer->getBirthday()->format('Y/m/d H:i:s'));
    }

    /** @test */
    function it_creates_customer_with_given_birthday_as_string(): void
    {
        $birthday = new \DateTimeImmutable('39 years ago');

        $customer = CustomerFactory::createOne(['birthday' => '39 years ago']);

        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $customer->getBirthday()->format('Y/m/d H:i:s'));
    }
}
