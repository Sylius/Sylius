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

namespace Sylius\Tests\DataFixtures\Foundry\MessageHandler;

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCurrency;
use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCustomer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneShopUser;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneShopUserHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shop_user_with_default_values(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch(new CreateOneShopUser());

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertNotNull($shopUser->getCustomer()->getEmail());
        $this->assertNotNull($shopUser->getCustomer()->getFirstName());
        $this->assertNotNull($shopUser->getCustomer()->getLastName());
        $this->assertNotNull($shopUser->getCustomer()->getPhoneNumber());
        $this->assertNotNull($shopUser->getCustomer()->getBirthday());
        $this->assertNull($shopUser->getPlainPassword());
        $this->assertNotNull($shopUser->getPassword());
        // $this->assertNotNull($shopUser->getCustomer()->getGroup());
    }

    /** @test */
    function it_creates_customer_with_given_email(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new CreateOneShopUser())->withEmail('shop@sylius.com'));

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals('shop@sylius.com', $shopUser->getCustomer()->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new CreateOneShopUser())->withFirstName('Marty'));

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals('Marty', $shopUser->getCustomer()->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new CreateOneShopUser())->withLastName('McFly'));

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals('McFly', $shopUser->getCustomer()->getLastName());
    }

    /** @test */
    function it_creates_male_customer(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUse */
        $shopUse = $bus->dispatch((new createOneShopUser())->male());

        $this->assertInstanceOf(ShopUserInterface::class, $shopUse->object());
        $this->assertEquals('m', $shopUse->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_female_customer(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new createOneShopUser())->female());

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals('f', $shopUser->getCustomer()->getGender());
    }

    /** @test */
    function it_creates_customer_with_given_phone_number(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new createOneShopUser())->withPhoneNumber('0102030405'));

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals('0102030405', $shopUser->getCustomer()->getPhoneNumber());
    }

    /** @test */
    function it_creates_customer_with_given_birthday(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        $birthday = new \DateTimeImmutable('39 years ago');

        /** @var ShopUserInterface|Proxy $shopUser */
        $shopUser = $bus->dispatch((new createOneShopUser())->withBirthday($birthday));

        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $shopUser->getCustomer()->getBirthday()->format('Y/m/d H:i:s'));
    }

    /** @test */
    function it_creates_customer_with_given_birthday_as_string(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        $birthday = new \DateTimeImmutable('39 years ago');
//
//        /** @var ShopUserInterface|Proxy $shopUser */
//        $shopUser = $bus->dispatch((new createOneShopUser())->withBirthday('39 years ago'));
//
//        $this->assertInstanceOf(ShopUserInterface::class, $shopUser->object());
//        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $shopUser->getCustomer()->getBirthday()->format('Y/m/d H:i:s'));
    }
}
