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
use Sylius\Bundle\CoreBundle\Fixture\ProductAttributeFixture;
use Sylius\Component\Attribute\Factory\AttributeFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductAttributeFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'product_attributes');
    }

    /**
     * @test
     */
    public function product_attributes_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_attributes_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['product_attributes' => ['Color', 'Weight']]],
            ['product_attributes' => [['name' => 'Color'], ['name' => 'Weight']]],
            'product_attributes.*.name'
        );
    }

    /**
     * @test
     */
    public function product_attribute_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['product_attributes' => [null]]], 'product_attributes');
        $this->assertPartialConfigurationIsInvalid([['product_attributes' => [['name' => null]]]], 'product_attributes');

        $this->assertConfigurationIsValid([['product_attributes' => [['name' => 'custom1']]]], 'product_attributes');
    }

    /**
     * @test
     */
    public function product_attribute_code_is_optional()
    {
        $this->assertConfigurationIsValid([['product_attributes' => [['code' => 'CUSTOM']]]], 'product_attributes.*.code');
    }

    /**
     * @test
     */
    public function product_attribute_type_is_optional()
    {
        $this->assertConfigurationIsValid([['product_attributes' => [['type' => 'text']]]], 'product_attributes.*.type');
        $this->assertConfigurationIsValid([['product_attributes' => [['type' => 'bool']]]], 'product_attributes.*.type');
    }

    /**
     * @test
     */
    public function product_attribute_type_must_exist()
    {
        $this->assertPartialConfigurationIsInvalid([['product_attributes' => [['type' => 'not_defined']]]], 'product_attributes.*.type');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductAttributeFixture(
            $this->getMockBuilder(AttributeFactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            ['text' => 'Text attribute', 'bool' => 'Boolean attribute']
        );
    }
}
