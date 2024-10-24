<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture;

final class ShippingMethodFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function shipping_methods_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function shipping_methods_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function shipping_method_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function shipping_method_may_be_toggled(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    /**
     * @test
     */
    public function shipping_method_zone_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['zone' => 'EUROPE']]]], 'custom.*.zone');
    }

    /**
     * @test
     */
    public function shipping_method_category_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['category' => 'BOOKS']]]], 'custom.*.category');
    }

    /**
     * @test
     */
    public function shipping_method_channels_are_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['channels' => ['CHN-1', 'CHN-2']]]]], 'custom.*.channels');
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['channels' => []]]]],
            ['custom' => [['channels' => []]]],
            'custom.*.channels',
        );
        $this->assertProcessedConfigurationEquals(
            [['custom' => [['channels' => null]]]],
            ['custom' => [[]]],
            'custom.*.channels',
        );
    }

    /**
     * @test
     */
    public function shipping_method_calculator_configuration_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['calculator' => [
            'type' => 'flat_rate',
            'configuration' => [],
        ]]]]], 'custom.*.calculator');
    }

    /**
     * @test
     */
    public function shipping_method_calculator_must_define_its_type(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => null]]]], 'custom.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => []]]]], 'custom.*.calculator');
        $this->assertPartialConfigurationIsInvalid([['custom' => [['calculator' => [
            'configuration' => ['option' => 'value'],
        ]]]]], 'custom.*.calculator');
    }

    /**
     * @test
     */
    public function shipping_method_tax_category(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['tax_category' => 'BOOKS']]]], 'custom.*.tax_category');
    }

    protected function getConfiguration(): ShippingMethodFixture
    {
        return new ShippingMethodFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
