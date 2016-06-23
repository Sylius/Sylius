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
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ShippingMethodFixtureTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function shipping_methods_are_optional()
    {
        $this->assertConfigurationIsValid([[]], 'custom');
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
    public function shipping_method_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function shipping_method_may_be_toggled()
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function shipping_method_zone_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['zone' => 'EUROPE']]]], 'custom.*.zone');
    }

    /**
     * @test
     */
    public function shipping_method_category_code_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['category' => 'BOOKS']]]], 'custom.*.category');
    }

    /**
     * @test
     */
    public function shipping_method_calculator_configuration_is_optional()
    {
        $this->assertConfigurationIsValid([['custom' => [['calculator' => [
            'type' => 'flat_rate',
            'configuration' => [],
        ]]]]], 'custom.*.calculator');
    }

    /**
     * @test
     */
    public function shipping_method_calculator_must_define_its_type()
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => null]]]], 'custom.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => []]]]], 'custom.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => [
            'configuration' => ['option' => 'value'],
        ]]]]], 'custom.*.calculator');
    }

    /**
     * {@inheritdoc}
     */
    protected function getConfiguration()
    {
        return new ShippingMethodFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
