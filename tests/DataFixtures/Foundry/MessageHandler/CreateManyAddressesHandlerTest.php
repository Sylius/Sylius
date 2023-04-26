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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateManyAddresses;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Test\Factories;

final class CreateManyAddressesHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_many_addresses_with_random_data(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var array $addresses */
        $addresses = $bus->dispatch(new CreateManyAddresses(5));

        $this->assertCount(5, $addresses);
    }

    /** @test */
    function it_creates_many_addresses_with_given_company(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var array $addresses */
        $addresses = $bus->dispatch((new CreateManyAddresses(5))->withCompany('Universal Pictures'));

        /** @var AddressInterface $address */
        $address = $addresses[0];

        $this->assertEquals('Universal Pictures', $address->getCompany());
    }

    /** @test */
    function it_creates_many_addresses_with_given_street(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var array $addresses */
        $addresses = $bus->dispatch((new CreateManyAddresses(5))->withStreet('9303 Lyon Drive, Lyon Estates'));

        /** @var AddressInterface $address */
        $address = $addresses[0];

        $this->assertEquals('9303 Lyon Drive, Lyon Estates', $address->getStreet());
    }

    /** @test */
    function it_creates_many_addresses_with_given_city(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var array $addresses */
        $addresses = $bus->dispatch((new CreateManyAddresses(5))->withCity('Hill Valley'));

        /** @var AddressInterface $address */
        $address = $addresses[0];

        $this->assertEquals('Hill Valley', $address->getCity());
    }
}
