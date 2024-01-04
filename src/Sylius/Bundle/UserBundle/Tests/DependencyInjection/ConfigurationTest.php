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

namespace Sylius\Bundle\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_has_default_configuration_for_user_resetting(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['resources' => ['admin' => ['user' => []]]]],
            ['resources' => [
                'admin' => [
                    'user' => [
                        'resetting' => [
                            'token' => [
                                'ttl' => 'P1D',
                                'length' => 16,
                                'field_name' => 'passwordResetToken',
                            ],
                            'pin' => [
                                'length' => 4,
                                'field_name' => 'passwordResetToken',
                            ],
                        ],
                    ],
                ],
            ]],
            'resources.*.user.resetting',
        );
    }

    /** @test */
    public function it_throws_an_exception_if_value_other_then_compatible_with_date_interval_is_declared_as_ttl(): void
    {
        $this->assertConfigurationIsInvalid(
            [['resources' => ['admin' => ['user' => ['resetting' => ['token' => ['ttl' => 'invalid']]]]]]],
            'Invalid format for TTL ""invalid"". Expected a string compatible with DateInterval.',
        );
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
