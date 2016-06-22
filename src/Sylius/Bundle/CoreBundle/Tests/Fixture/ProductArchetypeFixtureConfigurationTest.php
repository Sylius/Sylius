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
use Sylius\Bundle\CoreBundle\Fixture\ProductArchetypeFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductArchetypeFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_archetypes_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'product_archetypes');
    }

    /**
     * @test
     */
    public function product_archetypes_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_archetypes_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['product_archetypes' => ['custom1', 'custom2']]],
            ['product_archetypes' => [['name' => 'custom1'], ['name' => 'custom2']]],
            'product_archetypes.*.name'
        );
    }

    /**
     * @test
     */
    public function product_archetype_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['product_archetypes' => [null]]], 'product_archetypes');
        $this->assertPartialConfigurationIsInvalid([['product_archetypes' => [['name' => null]]]], 'product_archetypes');

        $this->assertConfigurationIsValid([['product_archetypes' => [['name' => 'custom1']]]], 'product_archetypes');
    }

    /**
     * @test
     */
    public function product_archetype_code_is_optional()
    {
        $this->assertConfigurationIsValid([['product_archetypes' => [['code' => 'CUSTOM']]]], 'product_archetypes.*.code');
    }

    /**
     * @test
     */
    public function product_archetype_product_options_are_optional()
    {
        $this->assertConfigurationIsValid([['product_archetypes' => [['product_options' => ['OPT-1', 'OPT-2']]]]], 'product_archetypes.*.product_options');
    }

    /**
     * @test
     */
    public function product_archetype_product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([['product_archetypes' => [['product_attributes' => ['ATTR-1', 'ATTR-2']]]]], 'product_archetypes.*.product_attributes');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductArchetypeFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
