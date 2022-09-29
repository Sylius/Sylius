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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\LocaleFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAttributeFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxonFactory;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
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
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withCode('007')->create();

        $this->assertEquals('007', $product->getCode());
    }

    /** @test */
    function it_creates_product_with_given_name(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withName('Back to the future DVD')->create();

        $this->assertEquals('Back to the future DVD', $product->getName());
        $this->assertEquals('Back_to_the_future_DVD', $product->getCode());
    }

    /** @test */
    function it_creates_enabled_product(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->enabled()->create();

        $this->assertTrue($product->isEnabled());
    }

    /** @test */
    function it_creates_disabled_product(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->disabled()->create();

        $this->assertFalse($product->isEnabled());
    }

    /** @test */
    function it_creates_tracked_product(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->tracked()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertTrue($variant->isTracked());
    }

    /** @test */
    function it_creates_untracked_product(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
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
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withShippingRequired()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertTrue($variant->isShippingRequired());
    }

    /** @test */
    function it_creates_product_with_shipping_not_required(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withShippingNotRequired()->create();

        /** @var ProductVariantInterface|false $variant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);

        $this->assertFalse($variant->isShippingRequired());
    }

    /** @test */
    function it_creates_product_with_given_variant_selection_method(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_CHOICE)->create();

        $this->assertEquals(ProductInterface::VARIANT_SELECTION_CHOICE, $product->getVariantSelectionMethod());
    }

    /** @test */
    function it_creates_product_with_given_tax_category(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withTaxCategory('clothing')->create();

        /** @var ProductVariantInterface $firstVariant */
        $variant = $product->getVariants()->first();
        $this->assertNotFalse($variant);
        $this->assertEquals('clothing', $variant->getTaxCategory()?->getCode());
    }

    /** @test */
    function it_creates_product_with_given_channel_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $channel = ChannelFactory::createOne();
        $product = ProductFactory::new()->withChannels([$channel])->create();

        $this->assertCount(1, $product->getChannels());
        $this->assertEquals($channel->object(), $product->getChannels()->first());
    }

    /** @test */
    function it_creates_product_with_given_channel(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $channel = ChannelFactory::createOne()->object();
        $product = ProductFactory::new()->withChannels([$channel])->create();

        $this->assertCount(1, $product->getChannels());
        $this->assertEquals($channel, $product->getChannels()->first());
    }

    /** @test */
    function it_creates_product_with_given_channel_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withChannels(['default_channel'])->create();

        $this->assertCount(1, $product->getChannels());
        $this->assertEquals('default_channel', $product->getChannels()->first()->getCode());
    }

    /** @test */
    function it_creates_product_with_given_main_taxon_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $taxon = TaxonFactory::createOne();
        $product = ProductFactory::new()->withMainTaxon($taxon)->create();

        $this->assertEquals($taxon->object(), $product->getMainTaxon());
    }

    /** @test */
    function it_creates_product_with_given_main_taxon(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $taxon = TaxonFactory::createOne()->object();
        $product = ProductFactory::new()->withMainTaxon($taxon)->create();

        $this->assertEquals($taxon, $product->getMainTaxon());
    }

    /** @test */
    function it_creates_product_with_given_main_taxon_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withMainTaxon('caps')->create();

        $this->assertEquals('caps', $product->getMainTaxon()->getCode());
    }

    /** @test */
    function it_creates_product_with_given_taxa_as_proxy(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $firstTaxon = TaxonFactory::createOne();
        $secondTaxon = TaxonFactory::createOne();
        $product = ProductFactory::new()->withTaxa([$firstTaxon, $secondTaxon])->create();

        $this->assertEquals($firstTaxon->object(), $product->getTaxons()->first());
        $this->assertEquals($secondTaxon->object(), $product->getTaxons()->last());
    }

    /** @test */
    function it_creates_product_with_given_taxa(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $firstTaxon = TaxonFactory::createOne()->object();
        $secondTaxon = TaxonFactory::createOne()->object();
        $product = ProductFactory::new()->withTaxa([$firstTaxon, $secondTaxon])->create();

        $this->assertEquals($firstTaxon, $product->getTaxons()->first());
        $this->assertEquals($secondTaxon, $product->getTaxons()->last());
    }

    /** @test */
    function it_creates_product_with_given_taxa_as_string(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        $product = ProductFactory::new()->withTaxa(['jeans', 'men_jeans'])->create();

        $this->assertEquals('jeans', $product->getTaxons()->first()->getCode());
        $this->assertEquals('men_jeans', $product->getTaxons()->last()->getCode());
    }

    /** @test */
    function it_creates_product_with_given_product_attributes(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        ProductAttributeFactory::new()->withType(ProductAttributeValueInterface::STORAGE_TEXT)->withCode('jeans_brand')->create();
        ProductAttributeFactory::new()->withType(ProductAttributeValueInterface::STORAGE_TEXT)->withCode('jeans_collection')->create();
        $product = ProductFactory::new()->withProductAttributes([
            'jeans_brand' => 'You are breathtaking',
            'jeans_collection' => 'Sylius Winter 2019',
        ])->create();

        $this->assertEquals('jeans_brand', $product->getAttributes()->first()->getCode());
    }

    /** @test */
    function it_creates_product_with_given_product_options(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();
        ProductOptionFactory::new()->withCode('jean_size')->withValues([
            'jeans_size_s' => 'S',
            'jeans_size_m' => 'M',
            'jeans_size_l' => 'L',
            'jeans_size_xl' => 'XL',
            'jeans_size_xxl'=> 'XXL',
        ])->create();

        $product = ProductFactory::new()->withProductOptions([
            'jeans_size'
        ])->create();

        $this->assertEquals('jeans_size', $product->getOptions()->first()->getCode());
    }

    /** @test */
    function it_creates_product_with_given_product_images(): void
    {
        LocaleFactory::new()->withCode('en_US')->create();

        $product = ProductFactory::new()->withImages([
            ['path' => '@SyliusCoreBundle/Resources/fixtures/jeans/man/jeans_02.jpg', 'type' => 'main'],
        ])->create();

        $firstImage = $product->getImages()->first() ?: null;

        $this->assertStringEndsWith('jeans_02.jpg', $firstImage?->getPath());
        $this->assertEquals('main', $firstImage?->getType());
    }
}
