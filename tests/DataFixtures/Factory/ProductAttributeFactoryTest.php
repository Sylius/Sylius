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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAttributeFactory;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class ProductAttributeFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_product_attribute(): void
    {
        $productAttribute = ProductAttributeFactory::createOne();

        $this->assertInstanceOf(ProductAttributeInterface::class, $productAttribute->object());
        $this->assertNotNull($productAttribute->getCode());
        $this->assertTrue($productAttribute->isTranslatable());
    }

    /** @test */
    function it_creates_product_attribute_with_given_code(): void
    {
        $productAttribute = ProductAttributeFactory::new()->withCode('brand')->create();

        $this->assertEquals('brand', $productAttribute->getCode());
    }

    /** @test */
    function it_creates_product_attribute_with_given_type(): void
    {
        $productAttribute = ProductAttributeFactory::new()->withType('textarea')->create();

        $this->assertEquals('textarea', $productAttribute->getType());
    }

    /** @test */
    function it_creates_product_attribute_with_translations_for_each_locales(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $productAttribute = ProductAttributeFactory::new()->create();

        $this->assertCount(2, $productAttribute->getTranslations());
    }

    /** @test */
    function it_creates_product_attribute_with_given_name(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $productAttribute = ProductAttributeFactory::new()->withName('Brand')->create();

        // test en_US translation
        $productAttribute->setCurrentLocale('en_US');
        $productAttribute->setFallbackLocale('en_US');
        $this->assertEquals('Brand', $productAttribute->getName());
        $this->assertEquals('Brand', $productAttribute->getCode());

        // test fr_FR translation
        $productAttribute->setCurrentLocale('fr_FR');
        $productAttribute->setFallbackLocale('fr_FR');
        $this->assertEquals('Brand', $productAttribute->getName());
        $this->assertEquals('Brand', $productAttribute->getCode());
    }

    /** @test */
    function it_creates_translatable_product_attribute(): void
    {
        $productAttribute = ProductAttributeFactory::new()->translatable()->create();

        $this->assertTrue($productAttribute->isTranslatable());
    }

    /** @test */
    function it_creates_untranslatable_product_attribute(): void
    {
        $productAttribute = ProductAttributeFactory::new()->untranslatable()->create();

        $this->assertFalse($productAttribute->isTranslatable());
    }

    /** @test */
    function it_creates_product_attribute_with_given_configuration(): void
    {
        $productAttribute = ProductAttributeFactory::new()->withConfiguration(['foo' => 'fighters'])->create();

        $this->assertEquals(['foo' => 'fighters'], $productAttribute->getConfiguration());
    }
}
