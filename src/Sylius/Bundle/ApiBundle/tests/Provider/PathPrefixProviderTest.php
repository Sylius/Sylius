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

namespace Tests\Sylius\Bundle\ApiBundle\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixes;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;

final class PathPrefixProviderTest extends TestCase
{
    private PathPrefixProvider $pathPrefixProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pathPrefixProvider = new PathPrefixProvider(
            '/api/v2',
            [PathPrefixes::ADMIN_PREFIX, PathPrefixes::SHOP_PREFIX],
        );
    }

    public function testImplementsThePathPrefixProviderInterface(): void
    {
        self::assertInstanceOf(PathPrefixProviderInterface::class, $this->pathPrefixProvider);
    }

    public function testReturnsNullIfTheGivenPathIsNotApiPath(): void
    {
        $this->assertNull($this->pathPrefixProvider->getPathPrefix('/old-api/shop/certain-route'));
    }

    public function testReturnsNullIfTheGivenPathDoesNotMatchApiPrefixes(): void
    {
        $this->assertNull($this->pathPrefixProvider->getPathPrefix('/api/v2/wrong/certain-route'));
    }

    public function testReturnsShopPrefixBasedOnTheGivenPath(): void
    {
        self::assertSame('shop', $this->pathPrefixProvider->getPathPrefix('/api/v2/shop/certain-route'));
    }

    public function testReturnsAdminPrefixBasedOnTheGivenPath(): void
    {
        self::assertSame('admin', $this->pathPrefixProvider->getPathPrefix('/api/v2/admin/certain-route'));
    }

    public function testReturnsPrefixFromApiRouteWithSlashes(): void
    {
        $this->pathPrefixProvider = new PathPrefixProvider('/api/long/route/name', [PathPrefixes::ADMIN_PREFIX]);

        self::assertSame(
            'admin',
            $this->pathPrefixProvider->getPathPrefix('/api/long/route/name/admin/certain-route'),
        );
    }
}
