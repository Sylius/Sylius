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

namespace Sylius\Bundle\ApiBundle\Tests\ApiPlatform\Bridge\Symfony\Routing;

use ApiPlatform\Core\Api\OperationType;
use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\CachedRouteNameResolver;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Symfony\Component\Cache\Exception\CacheException;

final class CachedRouteNameResolverTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_get_route_name_for_item_route_with_no_matching_route(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No item route associated with the type "AppBundle\\Entity\\User".');

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(false)->shouldBeCalled();

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldNotBeCalled();

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated->getRouteName('AppBundle\Entity\User', OperationType::ITEM, [])
            ->willThrow(
                new InvalidArgumentException('No item route associated with the type "AppBundle\Entity\User".'),
            )
            ->shouldBeCalled()
        ;

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );
        $cachedRouteNameResolver->getRouteName('AppBundle\Entity\User', OperationType::ITEM);
    }

    /**
     * @test
     */
    public function test_get_route_name_forItem_route_on_cache_miss(): void
    {
        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(false)->shouldBeCalledTimes(1);
        $cacheItem->set('certain_item_route')->willReturn($cacheItem)->shouldBeCalledTimes(1);

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->shouldBeCalledTimes(1)->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldBeCalledTimes(1)->willReturn(true);

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated
            ->getRouteName('AppBundle\Entity\User', false, [])
            ->willReturn('certain_item_route')->shouldBeCalledTimes(1)
        ;

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );

        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName('AppBundle\Entity\User', false),
        );
        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName('AppBundle\Entity\User', false),
            'Trigger the local cache',
        );
    }

    /**
     * @test
     */
    public function it_get_route_name_for_item_route_on_cache_hit(): void
    {
        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->shouldBeCalledTimes(1)->willReturn(true);
        $cacheItem->get()->shouldBeCalledTimes(1)->willReturn('certain_item_route');

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->shouldBeCalledTimes(1)->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldNotBeCalled();

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated->getRouteName(Argument::cetera())->shouldNotBeCalled();

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );

        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::ITEM,
            ),
        );
        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::ITEM,
            ),
            'Trigger the local cache',
        );
    }

    /**
     * @test
     */
    public function get_route_name_for_collection_route_with_no_matching_route(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No collection route associated with the type "AppBundle\\Entity\\User".');

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(false)->shouldBeCalled();

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldNotBeCalled();

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated
            ->getRouteName('AppBundle\Entity\User', OperationType::COLLECTION, [])
            ->willThrow(
                new InvalidArgumentException(
                    'No collection route associated with the type "AppBundle\Entity\User".',
                ),
            )
            ->shouldBeCalled()
        ;

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );
        $cachedRouteNameResolver->getRouteName(
            'AppBundle\Entity\User',
            OperationType::COLLECTION,
        );
    }

    /**
     * @test
     */
    public function get_route_name_for_collection_route_on_cache_miss(): void
    {
        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->shouldBeCalledTimes(1)->willReturn(false);
        $cacheItem->set('certain_collection_route')->willReturn($cacheItem)->shouldBeCalledTimes(1);

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->shouldBeCalledTimes(1)->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldBeCalledTimes(1)->willReturn(true);

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated
            ->getRouteName('AppBundle\Entity\User', true, [])
            ->willReturn('certain_collection_route')->shouldBeCalledTimes(1)
        ;

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );

        $this->assertSame(
            'certain_collection_route',
            $cachedRouteNameResolver->getRouteName('AppBundle\Entity\User', true),
        );
        $this->assertSame(
            'certain_collection_route',
            $cachedRouteNameResolver->getRouteName('AppBundle\Entity\User', true),
            'Trigger the local cache',
        );
    }

    /**
     * @test
     */
    public function get_route_name_for_collection_route_on_cache_hit(): void
    {
        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(true)->shouldBeCalledTimes(1);
        $cacheItem->get()->willReturn('certain_collection_route')->shouldBeCalledTimes(1);

        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool->getItem(Argument::type('string'))->shouldBeCalledTimes(1)->willReturn($cacheItem);
        $cacheItemPool->save($cacheItem)->shouldNotBeCalled();

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated->getRouteName(Argument::cetera())->shouldNotBeCalled();

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );

        $this->assertSame(
            'certain_collection_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::COLLECTION,
            ),
        );
        $this->assertSame(
            'certain_collection_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::COLLECTION,
            ),
            'Trigger the local cache',
        );
    }

    /**
     * @test
     */
    public function get_route_name_with_cache_item_throws_cache_exception(): void
    {
        $cacheItemPool = $this->prophesize(CacheItemPoolInterface::class);
        $cacheItemPool
            ->getItem(Argument::type('string'))
            ->shouldBeCalledTimes(1)
            ->willThrow(new CacheException())
        ;

        $decorated = $this->prophesize(RouteNameResolverInterface::class);
        $decorated
            ->getRouteName('AppBundle\Entity\User', OperationType::ITEM, [])
            ->willReturn('certain_item_route')->shouldBeCalledTimes(1)
        ;

        $pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);

        $cachedRouteNameResolver = new CachedRouteNameResolver(
            $cacheItemPool->reveal(),
            $decorated->reveal(),
            $pathPrefixProvider->reveal(),
        );

        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::ITEM,
            ),
        );
        $this->assertSame(
            'certain_item_route',
            $cachedRouteNameResolver->getRouteName(
                'AppBundle\Entity\User',
                OperationType::ITEM,
            ),
            'Trigger the local cache',
        );
    }
}
