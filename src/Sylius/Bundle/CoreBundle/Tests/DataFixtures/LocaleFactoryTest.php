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

namespace Sylius\Bundle\CoreBundle\Tests\DataFixtures;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class LocaleFactoryTest extends KernelTestCase
{
    use Factories;

    /** @test */
    function it_creates_locales(): void
    {
        $locale = LocaleFactory::new()->withoutPersisting()->create();

        $this->assertInstanceOf(LocaleInterface::class, $locale->object());
    }

    /** @test */
    function it_creates_locales_with_codes(): void
    {
        $locale = LocaleFactory::new()->withoutPersisting()->withCode('fr_FR')->create();

        $this->assertEquals('fr_FR', $locale->getCode());

        $locale = LocaleFactory::new()->withoutPersisting()->withCode()->create();

        $this->assertNotNull($locale->getCode());

        $locale = LocaleFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($locale->getCode());
    }

    /** @test */
    function it_creates_locales_with_default_locale_codes(): void
    {
        $locale = LocaleFactory::new()->withoutPersisting()->withDefaultLocaleCode()->create();

        $this->assertEquals('en_US', $locale->getCode());
    }
}
