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
use Sylius\Bundle\CoreBundle\Fixture\ProductAssociationFixture;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductAssociationFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function product_assoiations_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function product_associations_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function product_association_type_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['type' => 'type']]]], 'custom.*.type');
    }

    /**
     * @test
     */
    public function product_association_owner_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['owner' => 'product']]]], 'custom.*.owner');
    }

    /**
     * @test
     */
    public function product_association_associated_products_are_optional()
    {
        $this->assertConfigurationIsValid(
            [[
                'custom' => [['associated_products' => ['product-1', 'product-2']]]
            ]],
            'custom.*.associated_products')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ProductAssociationFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
