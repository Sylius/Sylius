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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Provider\Technical;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\VersionData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical\VersionDataProvider;

final class VersionDataProviderTest extends TestCase
{
    private VersionDataProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new VersionDataProvider();
    }

    public function test_it_provides_version_information(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(VersionData::class, $data);
        self::assertIsString($data->syliusVersion);
        self::assertIsString($data->phpVersion);
        self::assertIsString($data->symfonyVersion);
    }

    public function test_it_returns_php_version(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(VersionData::class, $data);
        self::assertSame(PHP_VERSION, $data->phpVersion);
    }

    public function test_it_returns_api_platform_version(): void
    {
        $data = $this->provider->provide();

        self::assertInstanceOf(VersionData::class, $data);
        self::assertNotNull($data->apiPlatformVersion);
    }
}
