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
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneCurrencyHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_currency_with_random_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var Currency|Proxy $currency */
        $currency = $bus->dispatch(new CreateOneCurrency());

        $this->assertInstanceOf(CurrencyInterface::class, $currency->object());
        $this->assertNotNull($currency->getCode());
        $this->assertTrue(Currencies::exists($currency->getCode()));
    }

    /** @test */
    function it_creates_currency_with_given_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var Currency|Proxy $currency */
        $currency = $bus->dispatch((new CreateOneCurrency())->withCode('EUR'));

        $this->assertInstanceOf(CurrencyInterface::class, $currency->object());
        $this->assertEquals('EUR', $currency->getCode());
    }
}
