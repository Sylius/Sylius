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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing;

use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Cache\CachedTrait;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Bundle\ApiBundle\Provider\RequestApiPathPrefixProviderInterface;

/**
 * @experimental
 *
 * This class is based on src/Bridge/Symfony/Routing/CachedRouteNameResolver.php, but has added logic for matching /shop, /admin prefixes
 */
final class CachedRouteNameResolver implements RouteNameResolverInterface
{
    use CachedTrait;

    /** @var RouteNameResolverInterface */
    private $decorated;

    /** @var RequestApiPathPrefixProviderInterface */
    private $requestApiPathPrefixProvider;

    public function __construct(
        CacheItemPoolInterface $cacheItemPool,
        RouteNameResolverInterface $decorated,
        RequestApiPathPrefixProviderInterface $requestApiPathPrefixProvider)
    {
        $this->cacheItemPool = $cacheItemPool;
        $this->decorated = $decorated;
        $this->requestApiPathPrefixProvider = $requestApiPathPrefixProvider;
    }

    public function getRouteName(string $resourceClass, $operationType /*, array $context = []*/): string
    {
        $requestPrefix = sprintf(
            'route_name_%s_',
            $this->requestApiPathPrefixProvider->getCurrentRequestPrefix()
        );

        $context = \func_num_args() > 2 ? func_get_arg(2) : [];

        $cacheKey = $requestPrefix . md5(
            serialize([$resourceClass, $operationType, $context['subresource_resources'] ?? null])
        );

        return $this->getCached($cacheKey, function () use ($resourceClass, $operationType, $context) {
            /** @psalm-suppress TooManyArguments */
            return $this->decorated->getRouteName($resourceClass, $operationType, $context);
        });
    }
}
