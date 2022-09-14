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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactory;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProductFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_product_with_random_data(): void
    {
        $product = ProductFactory::createOne();

        $this->assertInstanceOf(ProductInterface::class, $product->object());
        $this->assertNotNull($product->getCode());
        $this->assertNotNull($product->getName());
        $this->assertIsBool($product->isEnabled());
    }

    /** @test */
    function it_creates_product_with_given_code(): void
    {
        $product = ProductFactory::new()->withCode('007')->create();

        $this->assertEquals('007', $product->getCode());
    }

    /** @test */
    function it_creates_product_with_given_name(): void
    {
        $product = ProductFactory::new()->withName('Back to the future DVD')->create();

        $this->assertEquals('Back to the future DVD', $product->getName());
        $this->assertEquals('Back_to_the_future_DVD', $product->getCode());
    }

    /** @test */
    function it_creates_enabled_product(): void
    {
        $product = ProductFactory::new()->enabled()->create();

        $this->assertTrue($product->isEnabled());
    }

    /** @test */
    function it_creates_disabled_product(): void
    {
        $product = ProductFactory::new()->disabled()->create();

        $this->assertFalse($product->isEnabled());
    }
}
