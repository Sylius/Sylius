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
use Sylius\Bundle\CoreBundle\Fixture\TaxCategoryFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxCategoryFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function tax_categories_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'tax_categories');
    }

    /**
     * @test
     */
    public function tax_categories_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function tax_categories_can_be_populated_with_custom_names()
    {
        $this->assertProcessedConfigurationEquals(
            [['tax_categories' => ['Goods', 'Services']]],
            ['tax_categories' => [['name' => 'Goods'], ['name' => 'Services']]],
            'tax_categories'
        );
    }

    /**
     * @test
     */
    public function tax_category_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['tax_categories' => [null]]], 'tax_categories');
        $this->assertPartialConfigurationIsInvalid([['tax_categories' => [['name' => null]]]], 'tax_categories');

        $this->assertConfigurationIsValid([['tax_categories' => [['name' => 'custom1']]]], 'tax_categories');
    }

    /**
     * @test
     */
    public function tax_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_categories' => [['code' => 'CUSTOM']]]], 'tax_categories.*.code');
    }

    /**
     * @test
     */
    public function tax_category_description_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_categories' => [['description' => 'Lorem ipsum']]]], 'tax_categories.*.description');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new TaxCategoryFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock()
        );
    }
}
