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
use Sylius\Bundle\CoreBundle\Fixture\TaxonFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxonFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function taxons_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'taxons');
    }

    /**
     * @test
     */
    public function taxons_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function taxons_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['taxons' => ['Taxon 1', 'Taxon 2']]],
            ['taxons' => [['name' => 'Taxon 1'], ['name' => 'Taxon 2']]],
            'taxons'
        );
    }

    /**
     * @test
     */
    public function taxon_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['taxons' => [null]]], 'taxons');
        $this->assertPartialConfigurationIsInvalid([['taxons' => [['name' => null]]]], 'taxons');

        $this->assertConfigurationIsValid([['taxons' => [['name' => 'custom1']]]], 'taxons');
    }

    /**
     * @test
     */
    public function taxon_code_is_optional()
    {
        $this->assertConfigurationIsValid([['taxons' => [['code' => 'CUSTOM']]]], 'taxons.*.code');
    }

    /**
     * @test
     */
    public function taxon_parent_is_optional()
    {
        $this->assertConfigurationIsValid([['taxons' => [['parent' => 'PARENT-TAXON']]]], 'taxons.*.parent');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new TaxonFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
