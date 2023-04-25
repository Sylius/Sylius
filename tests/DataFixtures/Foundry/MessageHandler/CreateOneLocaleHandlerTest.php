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

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneLocale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneLocaleHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_locale_with_random_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var LocaleInterface|Proxy $locale */
        $locale = $bus->dispatch(new CreateOneLocale());

        $this->assertInstanceOf(LocaleInterface::class, $locale->object());
        $this->assertNotNull($locale->getCode());
    }

    /** @test */
    function it_creates_locale_with_given_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var LocaleInterface|Proxy $locale */
        $locale = $bus->dispatch((new CreateOneLocale())->withCode('fr_FR'));

        $this->assertInstanceOf(LocaleInterface::class, $locale->object());
        $this->assertEquals('fr_FR', $locale->getCode());
    }
}
