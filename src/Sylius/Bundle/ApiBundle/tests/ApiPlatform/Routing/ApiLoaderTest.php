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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\ApiPlatform\Routing\ApiLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

final class ApiLoaderTest extends TestCase
{
    use ProphecyTrait;

    private LoaderInterface|ObjectProphecy $baseApiLoader;

    private LoaderInterface $apiLoader;

    protected function setUp(): void
    {
        $this->baseApiLoader = $this->prophesize(LoaderInterface::class);

        $this->apiLoader = new ApiLoader(
            $this->baseApiLoader->reveal(),
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
        $routeCollection = $this->prophesize(RouteCollection::class);

        $this->baseApiLoader->load('.', 'api_platform')->willReturn($routeCollection);
        $routeCollection
            ->remove([
                'sylius_api_shop_currency_get',
                'sylius_api_shop_currency_get_collection',
            ])
            ->shouldBeCalled()
        ;

        $this->assertSame($routeCollection->reveal(), $this->apiLoader->load('.', 'api_platform'));
    }

    #[Test]
    public function it_uses_base_api_loader_for_supports_method(): void
    {
        $this->baseApiLoader->supports('.', 'api_platform')->willReturn(true);

        $this->assertTrue($this->apiLoader->supports('.', 'api_platform'));
    }

    #[Test]
    public function it_uses_base_api_loader_to_get_resolver(): void
    {
        $loaderResolver = $this->prophesize(LoaderResolverInterface::class);

        $this->baseApiLoader->getResolver()->willReturn($loaderResolver);

        $this->assertSame($loaderResolver->reveal(), $this->apiLoader->getResolver());
    }

    #[Test]
    public function it_uses_base_api_loader_to_set_resolver(): void
    {
        $loaderResolver = $this->prophesize(LoaderResolverInterface::class);

        $this->baseApiLoader->setResolver($loaderResolver->reveal())->shouldBeCalled();

        $this->apiLoader->setResolver($loaderResolver->reveal());
    }
}
