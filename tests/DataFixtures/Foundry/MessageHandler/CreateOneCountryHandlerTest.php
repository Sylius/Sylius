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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCountry;
use Sylius\Component\Addressing\Model\Country;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneCountryHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_country_with_random_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var Country|Proxy $country */
        $country = $bus->dispatch(new CreateOneCountry());

        $this->assertInstanceOf(CountryInterface::class, $country->object());
        $this->assertNotNull($country->getCode());
        $this->assertTrue(Countries::exists($country->getCode()));
    }

    /** @test */
    function it_creates_country_with_given_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var Country|Proxy $country */
        $country = $bus->dispatch((new CreateOneCountry())->withCode('PL'));

        $this->assertInstanceOf(CountryInterface::class, $country->object());
        $this->assertEquals('PL', $country->getCode());
    }
}
