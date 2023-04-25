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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\LocaleFactory;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Locales;
use Zenstruck\Foundry\Test\Factories;

final class LocaleFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_locale_with_random_code(): void
    {
        $locale = LocaleFactory::createOne();

        $this->assertInstanceOf(LocaleInterface::class, $locale->object());
        $this->assertNotNull($locale->getCode());
        $this->assertTrue(Locales::exists($locale->getCode()));
    }

    /** @test */
    function it_creates_locale_with_given_code(): void
    {
        $locale = LocaleFactory::createOne(['code' => 'fr_FR']);

        $this->assertEquals('fr_FR', $locale->getCode());
    }
}
