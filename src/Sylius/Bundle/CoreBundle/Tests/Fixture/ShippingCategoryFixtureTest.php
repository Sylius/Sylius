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
final class ShippingCategoryFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_requires_shipping_categories_node_to_be_set()
    {
        $this->assertPartialConfigurationIsInvalid([[]], 'shipping_categories');
        $this->assertPartialConfigurationIsInvalid([['shipping_categories' => null]], 'shipping_categories');
        $this->assertPartialConfigurationIsInvalid([['shipping_categories' => []]], 'shipping_categories');
    }

    /**
     * @test
     */
    public function its_shipping_categories_can_be_set()
    {
        $this->assertProcessedConfigurationEquals(
            [['shipping_categories' => ['Big', 'Small']]],
            ['shipping_categories' => ['Big', 'Small']],
            'shipping_categories'
        );
    }

    /**
     * @test
     */
    public function it_generates_random_shipping_categories_names_if_number_is_given()
    {
        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'shipping_categories',
            [['shipping_categories' => 3]]
        );

        $this->assertCount(3, $processedConfiguration['shipping_categories']);

        $processedConfiguration = (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'shipping_categories',
            [['shipping_categories' => '2']]
        );

        $this->assertCount(2, $processedConfiguration['shipping_categories']);
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
