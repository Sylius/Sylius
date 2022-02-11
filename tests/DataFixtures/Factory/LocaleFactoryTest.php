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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class LocaleFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_locale_with_random_code(): void
    {
        $locale = LocaleFactory::createOne();

        $this->assertInstanceOf(LocaleInterface::class, $locale->object());
        $this->assertNotNull($locale->getCode());
    }

    /** @test */
    function it_creates_locale_with_given_code(): void
    {
        $locale = LocaleFactory::new()->withCode('fr_FR')->create();

        $this->assertEquals('fr_FR', $locale->getCode());
    }

    /** @test */
    function it_creates_locale_with_default_code(): void
    {
        $locale = LocaleFactory::new()->withDefaultCode()->create();

        $this->assertEquals('en_US', $locale->getCode());
    }
}
