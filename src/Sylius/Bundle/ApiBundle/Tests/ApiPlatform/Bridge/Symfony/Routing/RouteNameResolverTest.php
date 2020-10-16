<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Tests\ApiPlatform\Bridge\Symfony\Routing;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\RouteNameResolver;
use Sylius\Bundle\ApiBundle\Provider\ApiPathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Provider\RequestApiPathPrefixProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class RouteNameResolverTest extends TestCase
{
    /**
     * @test
     */
    public function testConstruct(): void
    {
        $routerProphecy = $this->prophesize(RouterInterface::class);

        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $this->assertInstanceOf(RouteNameResolverInterface::class, $routeNameResolver);
    }

    /**
     * @test
     */
    public function testGetRouteNameForItemRouteWithNoMatchingRoute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No item route associated with the type "AppBundle\\Entity\\User".');

        $routeCollection = new RouteCollection();
        $routeCollection->add('certain_collection_route', new Route('/certain/collection/path', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_collection_operation_name' => 'certain_collection_op',
        ]));

        $routerProphecy = $this->prophesize(RouterInterface::class);
        $routerProphecy->getRouteCollection()->willReturn($routeCollection);

        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $routeNameResolver->getRouteName('AppBundle\Entity\User', OperationType::ITEM);
    }

    /**
     * @test
     */
    public function testGetRouteNameForItemRoute(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('certain_collection_route', new Route('/certain/collection/path', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_collection_operation_name' => 'certain_collection_op',
        ]));
        $routeCollection->add('certain_item_route', new Route('/certain/item/path/{id}', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_item_operation_name' => 'certain_item_op',
        ]));

        $routerProphecy = $this->prophesize(RouterInterface::class);
        $routerProphecy->getRouteCollection()->willReturn($routeCollection);

        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $actual = $routeNameResolver->getRouteName('AppBundle\Entity\User', OperationType::ITEM);

        $this->assertSame('certain_item_route', $actual);
    }

    /**
     * @test
     */
    public function testGetRouteNameForCollectionRouteWithNoMatchingRoute(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No collection route associated with the type "AppBundle\\Entity\\User".');

        $routeCollection = new RouteCollection();
        $routeCollection->add('certain_item_route', new Route('/certain/item/path/{id}', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_item_operation_name' => 'certain_item_op',
        ]));

        $routerProphecy = $this->prophesize(RouterInterface::class);
        $routerProphecy->getRouteCollection()->willReturn($routeCollection);

        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $routeNameResolver->getRouteName('AppBundle\Entity\User', OperationType::COLLECTION);
    }

    /**
     * @test
     */
    public function testGetRouteNameForCollectionRoute(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('certain_item_route', new Route('/certain/item/path/{id}', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_item_operation_name' => 'certain_item_op',
        ]));
        $routeCollection->add('certain_collection_route', new Route('/certain/collection/path', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_collection_operation_name' => 'certain_collection_op',
        ]));

        $routerProphecy = $this->prophesize(RouterInterface::class);
        $routerProphecy->getRouteCollection()->willReturn($routeCollection);

        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $actual = $routeNameResolver->getRouteName('AppBundle\Entity\User', OperationType::COLLECTION);

        $this->assertSame('certain_collection_route', $actual);
    }

    /**
     * @test
     */
    public function testGetRouteNameForSubresourceRoute(): void
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('a_certain_subresource_route', new Route('/a/certain/item/path/{id}', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_subresource_operation_name' => 'certain_other_item_op',
            '_api_subresource_context' => ['identifiers' => [[1, 'bar']]],
        ]));
        $routeCollection->add('b_certain_subresource_route', new Route('/b/certain/item/path/{id}', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_subresource_operation_name' => 'certain_item_op',
            '_api_subresource_context' => ['identifiers' => [[1, 'foo']]],
        ]));
        $routeCollection->add('certain_collection_route', new Route('/certain/collection/path', [
            '_api_resource_class' => 'AppBundle\Entity\User',
            '_api_collection_operation_name' => 'certain_collection_op',
        ]));

        $routerProphecy = $this->prophesize(RouterInterface::class);
        $routerProphecy->getRouteCollection()->willReturn($routeCollection);


        $requestApiPathPrefixProvider = $this->prophesize(RequestApiPathPrefixProviderInterface::class);

        $apiPathPrefixProvider = $this->prophesize(ApiPathPrefixProviderInterface::class);

        $routeNameResolver = new RouteNameResolver(
            $routerProphecy->reveal(),
            $requestApiPathPrefixProvider->reveal(),
            $apiPathPrefixProvider->reveal()
        );

        $actual = $routeNameResolver->getRouteName('AppBundle\Entity\User', OperationType::SUBRESOURCE, ['subresource_resources' => ['foo' => 1]]);

        $this->assertSame('b_certain_subresource_route', $actual);
    }
}
