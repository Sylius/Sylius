<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Sylius\Bundle\PaymentBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_turns_on_encryption_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['encryption' => ['enabled' => true]],
            'encryption.enabled'
        );
    }

    /** @test */
    public function its_encryption_can_be_turned_off(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['enabled' => false]]],
            ['encryption' => ['enabled' => false]],
            'encryption.enabled'
        );
    }

    /** @test */
    public function it_treats_null_like_true_in_gateways_encryption_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['gateways' => ['offline' => null]]]],
            ['encryption' => ['gateways' => ['offline' => true]]],
            'encryption.gateways'
        );
    }

    /** @test */
    public function it_can_configure_not_encrypted_gateways(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['gateways' => ['offline' => false]]]],
            ['encryption' => ['gateways' => ['offline' => false]]],
            'encryption.gateways'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
