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
use Sylius\Bundle\CoreBundle\Fixture\TaxRateFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Tax\Model\TaxCategoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxRateFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function tax_rates_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'tax_rates');
    }

    /**
     * @test
     */
    public function tax_rates_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function tax_rates_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['tax_rates' => ['custom1', 'custom2']]],
            ['tax_rates' => [['name' => 'custom1'], ['name' => 'custom2']]],
            'tax_rates'
        );
    }

    /**
     * @test
     */
    public function tax_rate_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['tax_rates' => [null]]], 'tax_rates');
        $this->assertPartialConfigurationIsInvalid([['tax_rates' => [['name' => null]]]], 'tax_rates');

        $this->assertConfigurationIsValid([['tax_rates' => [['name' => 'custom1']]]], 'tax_rates');
    }

    /**
     * @test
     */
    public function tax_rate_code_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['code' => 'CUSTOM']]]], 'tax_rates.*.code');
    }

    /**
     * @test
     */
    public function tax_rate_amount_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['amount' => 4.76]]]], 'tax_rates.*.amount');
        $this->assertPartialConfigurationIsInvalid([['tax_rates' => [['amount' => 'string']]]], 'tax_rates.*.amount');
    }

    /**
     * @test
     */
    public function tax_rate_may_be_included_in_price_or_not()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['included_in_price' => false]]]], 'tax_rates.*.included_in_price');
    }

    /**
     * @test
     */
    public function tax_rate_zone_code_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['zone' => 'EUROPE']]]], 'tax_rates.*.zone');
    }

    /**
     * @test
     */
    public function tax_rate_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['category' => 'BOOKS']]]], 'tax_rates.*.category');
    }

    /**
     * @test
     */
    public function tax_rate_calculator_is_optional()
    {
        $this->assertConfigurationIsValid([['tax_rates' => [['calculator' => 'custom']]]], 'tax_rates.*.calculator');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new TaxRateFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
