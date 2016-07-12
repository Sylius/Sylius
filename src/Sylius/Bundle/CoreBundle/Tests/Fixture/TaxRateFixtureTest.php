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
use Sylius\Bundle\CoreBundle\Fixture\TaxRateFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TaxRateFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function tax_rates_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function tax_rate_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function tax_rate_amount_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['amount' => 4.76]]]], 'custom.*.amount');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['amount' => 'string']]]], 'custom.*.amount');
    }

    /**
     * @test
     */
    public function tax_rate_may_be_included_in_price_or_not()
    {
        $this->assertConfigurationIsValid([['custom' => [['included_in_price' => false]]]], 'custom.*.included_in_price');
    }

    /**
     * @test
     */
    public function tax_rate_zone_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['zone' => 'EUROPE']]]], 'custom.*.zone');
    }

    /**
     * @test
     */
    public function tax_rate_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['category' => 'BOOKS']]]], 'custom.*.category');
    }

    /**
     * @test
     */
    public function tax_rate_calculator_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['calculator' => 'custom']]]], 'custom.*.calculator');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new TaxRateFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
