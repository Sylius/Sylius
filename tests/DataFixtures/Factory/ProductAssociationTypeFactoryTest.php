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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactory;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProductAssociationTypeFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_product_association_type_with_random_code(): void
    {
        $productAssociationType = ProductAssociationTypeFactory::createOne();

        $this->assertInstanceOf(ProductAssociationTypeInterface::class, $productAssociationType->object());
        $this->assertNotNull($productAssociationType->getCode());
    }

    /** @test */
    function it_creates_product_association_type_with_given_code(): void
    {
        $productAssociationType = ProductAssociationTypeFactory::new()->withCode('expansion')->create();

        $this->assertEquals('expansion', $productAssociationType->getCode());
    }

    /** @test */
    function it_creates_product_association_with_translations_on_each_locales(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $productAssociationType = ProductAssociationTypeFactory::new()->create();

        $this->assertCount(2, $productAssociationType->getTranslations());
    }

    /** @test */
    function it_creates_product_association_type_with_given_name_on_each_locales(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $productAssociationType = ProductAssociationTypeFactory::new()->withName('Expansion')->create();

        // test en_US translation
        $productAssociationType->setCurrentLocale('en_US');
        $productAssociationType->setFallbackLocale('en_US');

        $this->assertEquals('Expansion', $productAssociationType->getName());

        // test fr_FR translation
        $productAssociationType->setCurrentLocale('fr_FR');
        $productAssociationType->setFallbackLocale('fr_FR');

        $this->assertEquals('Expansion', $productAssociationType->getName());
    }
}
