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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneAddress;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneAddressHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_address_with_random_data(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch(new CreateOneAddress());

        $this->assertInstanceOf(AddressInterface::class, $address->object());
        $this->assertNotNull($address->getFirstName());
        $this->assertNotNull($address->getLastName());
        $this->assertNotNull($address->getStreet());
        $this->assertNotNull($address->getCity());
        $this->assertNotNull($address->getPostcode());
        $this->assertNotNull($address->getCountryCode());
        //$this->assertNotNull($address->getCustomer());
    }

    /** @test */
    function it_creates_address_with_given_first_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withFirstName('Marty'));

        $this->assertEquals('Marty', $address->getFirstName());
    }

    /** @test */
    function it_creates_address_with_given_last_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withLastName('McFly'));

        $this->assertEquals('McFly', $address->getLastName());
    }

    /** @test */
    function it_creates_address_with_given_phone_number(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withPhoneNumber('1955-1985-2015'));

        $this->assertEquals('1955-1985-2015', $address->getPhoneNumber());
    }

    /** @test */
    function it_creates_address_with_given_company(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withCompany('Universal Pictures'));

        $this->assertEquals('Universal Pictures', $address->getCompany());
    }

    /** @test */
    function it_creates_address_with_given_street(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withStreet('9303 Lyon Drive, Lyon Estates'));

        $this->assertEquals('9303 Lyon Drive, Lyon Estates', $address->getStreet());
    }

    /** @test */
    function it_creates_address_with_given_city(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var AddressInterface|Proxy $address */
        $address = $bus->dispatch((new CreateOneAddress())->withCity('Hill Valley'));

        $this->assertEquals('Hill Valley', $address->getCity());
    }
}
