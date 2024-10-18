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

namespace Sylius\Bundle\PaymentBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\DependencyInjection\Configuration;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_turns_on_encryption_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['encryption' => ['enabled' => true]],
            'encryption.enabled',
        );
    }

    /** @test */
    public function its_encryption_can_be_turned_off(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['enabled' => false]]],
            ['encryption' => ['enabled' => false]],
            'encryption.enabled',
        );
    }

    /** @test */
    public function it_treats_null_like_true_in_gateways_encryption_configuration(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['disabled_for_factories' => ['offline']]]],
            ['encryption' => ['disabled_for_factories' => ['offline']]],
            'encryption.disabled_for_factories',
        );
    }

    /** @test */
    public function it_can_configure_not_encrypted_gateways(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['encryption' => ['disabled_for_factories' => ['offline']]]],
            ['encryption' => ['disabled_for_factories' => ['offline']]],
            'encryption.disabled_for_factories',
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
