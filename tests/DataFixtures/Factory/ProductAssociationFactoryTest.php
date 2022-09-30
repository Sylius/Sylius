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
        LocaleFactory::new()->withCode('en_US')->create();
        ProductFactory::createMany(3);
        $productAssociation = ProductAssociationFactory::createOne();

        $this->assertInstanceOf(ProductAssociationInterface::class, $productAssociation->object());
        $this->assertNotNull($productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $type = ProductAssociationTypeFactory::createOne();
        $productAssociation = ProductAssociationFactory::new()->withType($type)->create();

        $this->assertSame($type->object(), $productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $type = ProductAssociationTypeFactory::createOne()->object();
        $productAssociation = ProductAssociationFactory::new()->withType($type)->create();

        $this->assertSame($type, $productAssociation->getType());
    }

    /** @test */
    function it_creates_product_association_with_given_type_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productAssociation = ProductAssociationFactory::new()->withType('collection')->create();

        $this->assertSame('collection', $productAssociation->getType()->getCode());
    }

    /** @test */
    function it_creates_product_association_with_given_owner_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::createOne();
        $productAssociation = ProductAssociationFactory::new()->withOwner($product)->create();

        $this->assertSame($product->object(), $productAssociation->getOwner());
    }

    /** @test */
    function it_creates_product_association_with_given_owner(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::createOne()->object();
        $productAssociation = ProductAssociationFactory::new()->withOwner($product)->create();

        $this->assertSame($product, $productAssociation->getOwner());
    }

    /** @test */
    function it_creates_product_association_with_given_owner_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $productAssociation = ProductAssociationFactory::new()->withOwner('Dirty_Dancing_DVD')->create();

        $this->assertNotNull($productAssociation->getOwner());
        $this->assertSame('Dirty_Dancing_DVD', $productAssociation->getOwner()->getCode());
    }

    /** @test */
    function it_creates_product_association_with_given_associated_products_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $firstProduct = ProductFactory::createOne();
        $secondProduct = ProductFactory::createOne();

        $productAssociation = ProductAssociationFactory::new()->withAssociatedProducts([$firstProduct, $secondProduct])->create();

        $firstProductAssociated = $productAssociation->getAssociatedProducts()->first() ?: null;
        $secondProductAssociated = $productAssociation->getAssociatedProducts()->last() ?: null;

        $this->assertNotNull($firstProductAssociated);
        $this->assertNotNull($secondProductAssociated);
        $this->assertSame($firstProduct->object(), $firstProductAssociated);
        $this->assertSame($secondProduct->object(), $secondProductAssociated);
    }

    /** @test */
    function it_creates_product_association_with_given_associated_products(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $firstProduct = ProductFactory::createOne()->object();
        $secondProduct = ProductFactory::createOne()->object();

        $productAssociation = ProductAssociationFactory::new()->withAssociatedProducts([$firstProduct, $secondProduct])->create();

        $firstProductAssociated = $productAssociation->getAssociatedProducts()->first() ?: null;
        $secondProductAssociated = $productAssociation->getAssociatedProducts()->last() ?: null;

        $this->assertNotNull($firstProductAssociated);
        $this->assertNotNull($secondProductAssociated);
        $this->assertSame($firstProduct, $firstProductAssociated);
        $this->assertSame($secondProduct, $secondProductAssociated);
    }

    /** @test */
    function it_creates_product_association_with_given_associated_products_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();

        $productAssociation = ProductAssociationFactory::new()->withAssociatedProducts(['Dirty_Dancing_DVD', 'Flashdance_DVD'])->create();

        $firstProductAssociated = $productAssociation->getAssociatedProducts()->first() ?: null;
        $secondProductAssociated = $productAssociation->getAssociatedProducts()->last() ?: null;

        $this->assertNotNull($firstProductAssociated);
        $this->assertNotNull($secondProductAssociated);
        $this->assertSame('Dirty_Dancing_DVD', $firstProductAssociated->getCode());
        $this->assertSame('Flashdance_DVD', $secondProductAssociated->getCode());
    }
}
