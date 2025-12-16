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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\EnvironmentData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\EnvironmentDataProvider;

final class EnvironmentDataProviderTest extends TestCase
{
    private EnvironmentDataProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new EnvironmentDataProvider('test');
    }

    public function test_it_provides_environment_information(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(EnvironmentData::class, $data);
    }

    public function test_it_returns_environment_structure(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(EnvironmentData::class, $data);
        self::assertIsString($data->app);
        self::assertIsString($data->os);
        self::assertIsBool($data->docker);
    }

    public function test_it_returns_app_environment(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(EnvironmentData::class, $data);
        self::assertSame('test', $data->app);
    }

    public function test_it_returns_os_family(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(EnvironmentData::class, $data);
        self::assertSame(PHP_OS_FAMILY, $data->os);
    }

    public function test_it_returns_boolean_for_docker(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(EnvironmentData::class, $data);
        self::assertIsBool($data->docker);
    }
}
