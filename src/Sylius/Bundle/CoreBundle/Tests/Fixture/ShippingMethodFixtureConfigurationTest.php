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
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingMethodFixtureConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function shipping_methods_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'shipping_methods');
    }

    /**
     * @test
     */
    public function shipping_methods_can_be_generated_randomly()
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function shipping_methods_can_be_defined_as_array_of_strings()
    {
        $this->assertProcessedConfigurationEquals(
            [['shipping_methods' => ['custom1', 'custom2']]],
            ['shipping_methods' => [['name' => 'custom1'], ['name' => 'custom2']]],
            'shipping_methods'
        );
    }

    /**
     * @test
     */
    public function shipping_method_name_is_required()
    {
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => [null]]], 'shipping_methods');
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => [['name' => null]]]], 'shipping_methods');

        $this->assertConfigurationIsValid([['shipping_methods' => [['name' => 'custom1']]]], 'shipping_methods');
    }

    /**
     * @test
     */
    public function shipping_method_code_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_methods' => [['code' => 'CUSTOM']]]], 'shipping_methods.*.code');
    }

    /**
     * @test
     */
    public function shipping_method_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['shipping_methods' => [['enabled' => false]]]], 'shipping_methods.*.enabled');
    }

    /**
     * @test
     */
    public function shipping_method_zone_code_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_methods' => [['zone' => 'EUROPE']]]], 'shipping_methods.*.zone');
    }

    /**
     * @test
     */
    public function shipping_method_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_methods' => [['category' => 'BOOKS']]]], 'shipping_methods.*.category');
    }

    /**
     * @test
     */
    public function shipping_method_calculator_configuration_is_optional()
    {
        $this->assertConfigurationIsValid([['shipping_methods' => [['calculator' => [
            'type' => 'flat_rate',
            'configuration' => [],
        ]]]]], 'shipping_methods.*.calculator');
    }

    /**
     * @test
     */
    public function shipping_method_calculator_must_define_its_type()
    {
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => [['calculator' => null]]]], 'shipping_methods.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => [['calculator' => []]]]], 'shipping_methods.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['shipping_methods' => [['calculator' => [
            'configuration' => ['option' => 'value'],
        ]]]]], 'shipping_methods.*.calculator');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ShippingMethodFixture(
            $this->getMockBuilder(FactoryInterface::class)->getMock(),
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock(),
            $this->getMockBuilder(RepositoryInterface::class)->getMock()
        );
    }
}
