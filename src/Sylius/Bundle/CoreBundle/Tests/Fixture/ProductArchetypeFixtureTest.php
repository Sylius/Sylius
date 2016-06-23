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
use Sylius\Bundle\CoreBundle\Fixture\ProductArchetypeFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductArchetypeFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_archetypes_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function product_archetype_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function product_archetype_product_options_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['product_options' => ['OPT-1', 'OPT-2']]]]], 'custom.*.product_options');
    }

    /**
     * @test
     */
    public function product_archetype_product_attributes_are_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['product_attributes' => ['ATTR-1', 'ATTR-2']]]]], 'custom.*.product_attributes');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductArchetypeFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
