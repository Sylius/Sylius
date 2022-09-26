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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactory;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProductAssociationFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_product_association_with_default_values(): void
    {
        ProductFactory::createMany(3);
        $productAssociation = ProductAssociationFactory::createOne();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertNotNull($productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type_as_proxy(): void
    {
        $type = ProductAssociationTypeFactory::createOne();
        $productAssociation = ProductAssociationFactory::new()->withType($type)->create();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertSame($type->object(), $productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type(): void
    {
        $type = ProductAssociationTypeFactory::createOne()->object();
        $productAssociation = ProductAssociationFactory::new()->withType($type)->create();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertSame($type, $productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type_as_string(): void
    {
        $productAssociation = ProductAssociationFactory::new()->withType('collection')->create();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertSame('collection', $productAssociation->getType()->getCode());
    }

    /** @test */
    function it_creates_product_association_with_given_owner(): void
    {
        $product = ProductFactory::createOne();
        $productAssociation = ProductAssociationFactory::new()->withOwner($product)->create();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertSame($product->object(), $productAssociation->getOwner());
    }
}
