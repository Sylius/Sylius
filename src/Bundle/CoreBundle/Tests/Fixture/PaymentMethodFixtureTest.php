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
use Sylius\Bundle\CoreBundle\Fixture\PaymentMethodFixture;

final class PaymentMethodFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function payment_methods_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /**
     * @test
     */
    public function payment_methods_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /**
     * @test
     */
    public function payment_method_code_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['code' => 'CUSTOM']]]], 'custom.*.code');
    }

    /**
     * @test
     */
    public function payment_method_gateway_name_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['gatewayName' => 'Online']]]], 'custom.*.gatewayName');
    }

    /**
     * @test
     */
    public function payment_method_gateway_factory_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['gatewayFactory' => 'offline']]]], 'custom.*.gatewayFactory');
    }

    /**
     * @test
     */
    public function payment_method_gateway_configuration_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['gatewayConfig' => []]]]], 'custom.*.gatewayConfig');
    }

    /**
     * @test
     */
    public function payment_method_channels_are_optional(): void
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
    public function payment_method_instructions_configuration_must_by_string(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['instructions' => 'test']]]], 'custom.*.instructions');
        $this->assertConfigurationIsInvalid([['custom' => [['instructions' => ['test']]]]]);
    }

    /**
     * @test
     */
    public function payment_method_instructions_configuration_can_be_null(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['instructions' => null]]]], 'custom.*.instructions');
    }

    /**
     * @test
     */
    public function payment_method_instructions_configuration_default_null(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['custom' => [[]]]],
            ['custom' => [[]]],
            'custom.*.instructions',
        );
    }

    /**
     * @test
     */
    public function payment_method_instructions_configuration_is_optional(): void
    {
        $this->assertConfigurationIsValid([['custom' => [[]]]], 'custom.*.instructions');
    }

    /**
     * @test
     */
    public function payment_method_gateway_configuration_must_by_array(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['gatewayConfig' => ['username' => 'USERNAME']]]]], 'custom.*.gatewayConfig');
        $this->assertConfigurationIsInvalid([['custom' => [['gatewayConfig' => 'USERNAME']]]]);
    }

    /**
     * @test
     */
    public function payment_method_may_be_toggled(): void
    {
        $this->assertConfigurationIsValid([['custom' => [['enabled' => false]]]], 'custom.*.enabled');
    }

    protected function getConfiguration(): PaymentMethodFixture
    {
        return new PaymentMethodFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock(),
        );
    }
}
