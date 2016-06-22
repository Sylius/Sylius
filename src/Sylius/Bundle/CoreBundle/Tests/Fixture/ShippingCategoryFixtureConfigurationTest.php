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
use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\CoreBundle\Fixture\ShippingCategoryFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingCategoryFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function shipping_categories_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'shipping_categories');
    }

    /**
     * @test
     */
    public function shipping_categories_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function shipping_categories_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['shipping_categories' => ['Big', 'Small']]],
            ['shipping_categories' => [['name' => 'Big'], ['name' => 'Small']]],
            'shipping_categories'
        );
    }

    /**
     * @test
     */
    public function shipping_category_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['shipping_categories' => [null]]], 'shipping_categories');
        $this->assertPartialConfigurationIsInvalid([['shipping_categories' => [['name' => null]]]], 'shipping_categories');

        $this->assertConfigurationIsValid([['shipping_categories' => [['name' => 'custom1']]]], 'shipping_categories');
    }

    /**
     * @test
     */
    public function shipping_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_categories' => [['code' => 'CUSTOM']]]], 'shipping_categories.*.code');
    }

    /**
     * @test
     */
    public function shipping_category_description_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_categories' => [['description' => 'Lorem ipsum']]]], 'shipping_categories.*.description');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ShippingCategoryFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock()
        );
    }
}
