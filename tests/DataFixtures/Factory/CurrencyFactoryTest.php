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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CurrencyFactory;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CurrencyFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_currencies(): void
    {
        $currency = CurrencyFactory::new()->create();

        $this->assertInstanceOf(CurrencyInterface::class, $currency->object());
    }

    /** @test */
    function it_creates_currencies_with_codes(): void
    {
        $currency = CurrencyFactory::new()->withCode('EUR')->create();

        $this->assertEquals('EUR', $currency->getCode());

        $currency = CurrencyFactory::new()->create();

        $this->assertNotNull($currency->getCode());
    }
}
