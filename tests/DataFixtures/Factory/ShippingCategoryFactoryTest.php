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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShippingCategoryFactory;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShippingCategoryFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shipping_category(): void
    {
        $shippingCategory = ShippingCategoryFactory::createOne();

        $this->assertInstanceOf(ShippingCategoryInterface::class, $shippingCategory->object());
        $this->assertNotNull($shippingCategory->getCode());
    }

    /** @test */
    function it_creates_shipping_category_with_given_code(): void
    {
        $shippingCategory = ShippingCategoryFactory::new()->withCode('SC2')->create();

        $this->assertEquals('SC2', $shippingCategory->getCode());
    }

    /** @test */
    function it_creates_shipping_category_with_given_name(): void
    {
        $shippingCategory = ShippingCategoryFactory::new()->withName('Shipping category one')->create();

        $this->assertEquals('Shipping category one', $shippingCategory->getName());
        $this->assertEquals('Shipping_category_one', $shippingCategory->getCode());
    }

    /** @test */
    function it_creates_shipping_category_with_given_description(): void
    {
        $shippingCategory = ShippingCategoryFactory::new()->withDescription('One category to rule them all.')->create();

        $this->assertEquals('One category to rule them all.', $shippingCategory->getDescription());
    }
}
