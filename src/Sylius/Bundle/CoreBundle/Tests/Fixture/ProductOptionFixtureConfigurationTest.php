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
use Sylius\Bundle\CoreBundle\Fixture\ProductOptionFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductOptionFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_options_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'product_options');
    }

    /**
     * @test
     */
    public function product_options_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_options_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['product_options' => ['Color', 'Weight']]],
            ['product_options' => [['name' => 'Color'], ['name' => 'Weight']]],
            'product_options.*.name'
        );
    }

    /**
     * @test
     */
    public function product_option_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['product_options' => [null]]], 'product_options');
        $this->assertPartialConfigurationIsInvalid([['product_options' => [['name' => null]]]], 'product_options');

        $this->assertConfigurationIsValid([['product_options' => [['name' => 'custom1']]]], 'product_options');
    }

    /**
     * @test
     */
    public function product_option_code_is_optional()
    {
        $this->assertConfigurationIsValid([['product_options' => [['code' => 'CUSTOM']]]], 'product_options.*.code');
    }

    /**
     * @test
     */
    public function product_option_values_are_optional()
    {
        $this->assertConfigurationIsValid([['product_options' => [['values' => ['code' => 'value']]]]], 'product_options.*.values');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductOptionFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
