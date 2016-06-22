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
use Sylius\Bundle\CoreBundle\Fixture\ProductAttributeFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductAttributeFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function product_attribute_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function product_attribute_type_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['type' => 'text']]]], 'custom.*.type');
        $this->assertConfigurationIsValid([['custom' => [['type' => 'bool']]]], 'custom.*.type');
    }

    /**
     * @test
     */
    public function product_attribute_type_must_exist()
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['type' => 'not_defined']]]], 'custom.*.type');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductAttributeFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
            ['text' => 'Text attribute', 'bool' => 'Boolean attribute']
        );
    }
}
