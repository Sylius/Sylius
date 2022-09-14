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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactory;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ProductFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_product_with_random_data(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::createOne();

        $this->assertInstanceOf(ProductInterface::class, $product->object());
        $this->assertNotNull($product->getCode());
        $this->assertNotNull($product->getName());
        $this->assertIsBool($product->isEnabled());
        $this->assertSame(ProductInterface::VARIANT_SELECTION_MATCH, $product->getVariantSelectionMethod());

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertNotNull($product->getShortDescription());
        $this->assertNotNull($product->getDescription());
        $this->assertFalse($variant->isTracked());
        $this->assertTrue($variant->isShippingRequired());
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

    /** @test */
    function it_creates_tracked_product(): void
    {
        $product = ProductFactory::new()->tracked()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertTrue($variant->isTracked());
    }

    /** @test */
    function it_creates_untracked_product(): void
    {
        $product = ProductFactory::new()->untracked()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertFalse($variant->isTracked());
    }

    /** @test */
    function it_creates_product_with_translations_on_each_locales(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        LocaleFactory::new()->withCode('fr_FR')->create();

        $product = ProductFactory::new()->create();

        $this->assertCount(2, $product->getTranslations());
    }

    /** @test */
    function it_creates_product_with_given_slug(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withSlug('custom-slug')->create();

        $this->assertEquals('custom-slug', $product->getSlug());
    }

    /** @test */
    function it_creates_product_with_given_short_description(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withShortDescription('It`s about time.')->create();

        $this->assertEquals('It`s about time.', $product->getShortDescription());
    }

    /** @test */
    function it_creates_product_with_given_description(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withDescription('It`s about time.')->create();

        $this->assertEquals('It`s about time.', $product->getDescription());
    }

    /** @test */
    function it_creates_product_with_shipping_required(): void
    {
        $product = ProductFactory::new()->withShippingRequired()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertTrue($variant->isShippingRequired());
    }

    /** @test */
    function it_creates_product_with_shipping_not_required(): void
    {
        $product = ProductFactory::new()->withShippingNotRequired()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertFalse($variant->isShippingRequired());
    }

    /** @test */
    function it_creates_product_with_given_variant_selection_method(): void
    {
        $product = ProductFactory::new()->withVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_CHOICE)->create();

        $this->assertEquals(ProductInterface::VARIANT_SELECTION_CHOICE, $product->getVariantSelectionMethod());
    }
}
