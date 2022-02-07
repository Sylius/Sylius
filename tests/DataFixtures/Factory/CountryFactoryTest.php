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
use Zenstruck\Foundry\Test\ResetDatabase;

final class CountryFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_countries(): void
    {
        $country = CountryFactory::new()->create();

        $this->assertInstanceOf(CountryInterface::class, $country->object());
    }

    /** @test */
    function it_creates_countries_with_codes(): void
    {
        $country = CountryFactory::new()->withCode('PL')->create();

        $this->assertEquals('PL', $country->getCode());

        $country = CountryFactory::new()->create();

        $this->assertNotNull($country->getCode());
    }

    /** @test */
    function it_creates_enabled_countries(): void
    {
        $country = CountryFactory::new()->enabled()->create();

        $this->assertTrue($country->isEnabled());
    }

    /** @test */
    function it_creates_disabled_countries(): void
    {
        $country = CountryFactory::new()->disabled()->create();

        $this->assertFalse($country->isEnabled());
    }
}
