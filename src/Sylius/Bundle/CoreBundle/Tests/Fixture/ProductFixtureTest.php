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
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ProductFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function products_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function product_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function product_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function product_description_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['description' => 'Foo bar baz']]]], 'custom.*.description');
    }

    /**
     * @test
     */
    public function product_short_description_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['short_description' => 'Foo bar']]]], 'custom.*.short_description');
    }

    /**
     * @test
     */
    public function product_main_taxon_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['main_taxon' => 'TXN-0']]]], 'custom.*.main_taxon');
    }

    /**
     * @test
     */
    public function product_taxons_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['taxons' => ['TXN-1', 'TXN-2']]]]], 'custom.*.taxons');
    }

    /**
     * @test
     */
    public function product_channels_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['channels' => ['CHN-1', 'CHN-2']]]]], 'custom.*.channels');
    }

    /**
     * @test
     */
    public function product_product_options_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['product_options' => ['OPT-1', 'OPT-2']]]]], 'custom.*.product_options');
    }

    /**
     * @test
     */
    public function product_product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['product_attributes' => ['ATTR-1', 'ATTR-2']]]]], 'custom.*.product_attributes');
    }

    /**
     * @test
     */
    public function product_images_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['images' => ['../image/path1.jpg', '../image/path2.jpg']]]]], 'custom.*.images');
    }

    /**
     * @test
     */
    public function product_can_require_shipping()
    {
        $this->assertConfigurationIsValid([['custom' => [['shipping_required' => false]]]], 'custom.*.shipping_required');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
