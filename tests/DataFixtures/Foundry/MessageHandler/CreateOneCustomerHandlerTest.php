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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneCustomerHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_with_default_values(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch(new CreateOneCustomer());

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
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withEmail('shop@sylius.com'));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('shop@sylius.com', $customer->getEmail());
    }

    /** @test */
    function it_creates_customer_with_given_first_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withFirstName('Marty'));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('Marty', $customer->getFirstName());
    }

    /** @test */
    function it_creates_customer_with_given_last_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withLastName('McFly'));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('McFly', $customer->getLastName());
    }

    /** @test */
    function it_creates_male_customer(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->male());

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('m', $customer->getGender());
    }

    /** @test */
    function it_creates_female_customer(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->female());

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('f', $customer->getGender());
    }

    /** @test */
    function it_creates_customer_with_given_phone_number(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withPhoneNumber('0102030405'));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals('0102030405', $customer->getPhoneNumber());
    }

    /** @test */
    function it_creates_customer_with_given_birthday(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        $birthday = new \DateTimeImmutable('39 years ago');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withBirthday($birthday));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $customer->getBirthday()->format('Y/m/d H:i:s'));
    }

    /** @test */
    function it_creates_customer_with_given_birthday_as_string(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        $birthday = new \DateTimeImmutable('39 years ago');

        /** @var CustomerInterface|Proxy $customer */
        $customer = $bus->dispatch((new CreateOneCustomer())->withBirthday('39 years ago'));

        $this->assertInstanceOf(CustomerInterface::class, $customer->object());
        $this->assertEquals($birthday->format('Y/m/d H:i:s'), $customer->getBirthday()->format('Y/m/d H:i:s'));
    }
}
