<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ProductFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function products_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'products');
    }

    /**
     * @test
     */
    public function products_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function products_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['products' => ['custom1', 'custom2']]],
            ['products' => [['name' => 'custom1'], ['name' => 'custom2']]],
            'products.*.name'
        );
    }

    /**
     * @test
     */
    public function product_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['products' => [null]]], 'products');
        $this->assertPartialConfigurationIsInvalid([['products' => [['name' => null]]]], 'products');

        $this->assertConfigurationIsValid([['products' => [['name' => 'custom1']]]], 'products');
    }

    /**
     * @test
     */
    public function product_code_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['code' => 'CUSTOM']]]], 'products.*.code');
    }

    /**
     * @test
     */
    public function product_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['products' => [['enabled' => false]]]], 'products.*.enabled');
    }

    /**
     * @test
     */
    public function product_description_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['description' => 'Foo bar baz']]]], 'products.*.description');
    }

    /**
     * @test
     */
    public function product_short_description_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['short_description' => 'Foo bar']]]], 'products.*.short_description');
    }

    /**
     * @test
     */
    public function product_main_taxon_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['main_taxon' => 'TXN-0']]]], 'products.*.main_taxon');
    }

    /**
     * @test
     */
    public function product_archetype_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['archetype' => 'ARCHETYPE']]]], 'products.*.archetype');
    }

    /**
     * @test
     */
    public function product_shipping_category_is_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['shipping_category' => 'SHP-0']]]], 'products.*.shipping_category');
    }

    /**
     * @test
     */
    public function product_taxons_are_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['taxons' => ['TXN-1', 'TXN-2']]]]], 'products.*.taxons');
    }

    /**
     * @test
     */
    public function product_channels_are_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['channels' => ['CHN-1', 'CHN-2']]]]], 'products.*.channels');
    }

    /**
     * @test
     */
    public function product_product_options_are_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['product_options' => ['OPT-1', 'OPT-2']]]]], 'products.*.product_options');
    }

    /**
     * @test
     */
    public function product_product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([['products' => [['product_attributes' => ['ATTR-1', 'ATTR-2']]]]], 'products.*.product_attributes');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(VariantGeneratorInterface::class)->getMock(),
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
