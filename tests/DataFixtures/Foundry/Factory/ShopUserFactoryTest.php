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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\ShopUserFactory;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShopUserFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shop_user_with_default_values(): void
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
        //$this->assertNotNull($shopUser->getCustomer()->getGroup());
    }

    /** @test */
    function it_creates_customer_with_given_email(): void
    {
        $shopUser = ShopUserFactory::createOne(['email' => 'shop@sylius.com']);

        $this->assertEquals('shop@sylius.com', $shopUser->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        $shopUser = ShopUserFactory::createOne(['firstName' => 'Marty']);

        $this->assertEquals('Marty', $shopUser->getCustomer()->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        $shopUser = ShopUserFactory::createOne(['lastName' => 'McFly']);

        $this->assertEquals('McFly', $shopUser->getCustomer()->getLastName());
    }

    /** @test */
    function it_creates_customer_with_given_password(): void
    {
        $shopUser = ShopUserFactory::new()->withoutPersisting()->withAttributes(['password' => 'passw0rd'])->create();

        $this->assertEquals('passw0rd', $shopUser->getPlainPassword());
    }
}
