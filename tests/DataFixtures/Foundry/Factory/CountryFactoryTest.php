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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Factory\CountryFactory;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Intl\Countries;
use Zenstruck\Foundry\Test\Factories;

final class CountryFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_country_with_random_code(): void
    {
        $country = CountryFactory::createOne();

        $this->assertInstanceOf(CountryInterface::class, $country->object());
        $this->assertNotNull($country->getCode());
        $this->assertTrue(Countries::exists($country->getCode()));
    }

    /** @test */
    function it_creates_country_with_given_code(): void
    {
        $country = CountryFactory::createOne(['code' => 'PL']);

        $this->assertEquals('PL', $country->getCode());
    }
}
