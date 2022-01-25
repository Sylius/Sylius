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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class CountryFactoryTest extends KernelTestCase
{
    use Factories;

    /** @test */
    function it_creates_countries(): void
    {
        $country = CountryFactory::new()->withoutPersisting()->create();

        $this->assertInstanceOf(CountryInterface::class, $country->object());
    }

    /** @test */
    function it_creates_countries_with_codes(): void
    {
        $country = CountryFactory::new()->withoutPersisting()->withCode('PL')->create();

        $this->assertEquals('PL', $country->getCode());

        $country = CountryFactory::new()->withoutPersisting()->withCode()->create();

        $this->assertNotNull($country->getCode());

        $country = CountryFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($country->getCode());
    }

    /** @test */
    function it_creates_enabled_countries(): void
    {
        $country = CountryFactory::new()->withoutPersisting()->enabled()->create();

        $this->assertTrue($country->isEnabled());
    }

    /** @test */
    function it_creates_disabled_countries(): void
    {
        $country = CountryFactory::new()->withoutPersisting()->disabled()->create();

        $this->assertFalse($country->isEnabled());
    }
}
