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

namespace Tests\Sylius\Bundle\ApiBundle\ApiPlatform\Routing;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Routing\ApiLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

#[AllowMockObjectsWithoutExpectations]
final class ApiLoaderTest extends TestCase
{
    private LoaderInterface&MockObject $baseApiLoader;

    private LoaderInterface $apiLoader;

    protected function setUp(): void
    {
        $this->baseApiLoader = $this->createMock(LoaderInterface::class);

        $this->apiLoader = new ApiLoader(
            $this->baseApiLoader,
            [
                'sylius_api_shop_currency_get',
                'sylius_api_shop_currency_get_collection',
            ],
        );
    }

    #[Test]
    public function it_implements_the_loader_interface(): void
    {
        self::assertInstanceOf(LoaderInterface::class, $this->apiLoader);
    }

    #[Test]
    public function it_removes_routes_from_route_collection_loaded_by_base_api_loader(): void
    {
        $routeCollection = $this->createMock(RouteCollection::class);

        $this->baseApiLoader
            ->expects($this->once())
            ->method('load')
            ->with('.', 'api_platform')
            ->willReturn($routeCollection);

        $routeCollection
            ->expects($this->once())
            ->method('remove')
            ->with([
                'sylius_api_shop_currency_get',
                'sylius_api_shop_currency_get_collection',
            ]);

        $this->assertSame($routeCollection, $this->apiLoader->load('.', 'api_platform'));
    }

    #[Test]
    public function it_uses_base_api_loader_for_supports_method(): void
    {
        $this->baseApiLoader
            ->expects($this->once())
            ->method('supports')
            ->with('.', 'api_platform')
            ->willReturn(true);

        $this->assertTrue($this->apiLoader->supports('.', 'api_platform'));
    }

    #[Test]
    public function it_uses_base_api_loader_to_get_resolver(): void
    {
        $loaderResolver = $this->createMock(LoaderResolverInterface::class);

        $this->baseApiLoader
            ->expects($this->once())
            ->method('getResolver')
            ->willReturn($loaderResolver);

        $this->assertSame($loaderResolver, $this->apiLoader->getResolver());
    }

    #[Test]
    public function it_uses_base_api_loader_to_set_resolver(): void
    {
        $loaderResolver = $this->createMock(LoaderResolverInterface::class);

        $this->baseApiLoader
            ->expects($this->once())
            ->method('setResolver')
            ->with($loaderResolver);

        $this->apiLoader->setResolver($loaderResolver);
    }
}
