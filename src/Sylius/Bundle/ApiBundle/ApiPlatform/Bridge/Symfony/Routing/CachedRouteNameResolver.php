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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing;

use ApiPlatform\Core\Bridge\Symfony\Routing\RouteNameResolverInterface;
use ApiPlatform\Core\Cache\CachedTrait;
use Psr\Cache\CacheItemPoolInterface;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;

/**
 * This class is based on src/Bridge/Symfony/Routing/CachedRouteNameResolver.php, but has added logic for matching /shop, /admin prefixes
 */
final class CachedRouteNameResolver implements RouteNameResolverInterface
{
    use CachedTrait;

    public function __construct(
        CacheItemPoolInterface $cacheItemPool,
        private RouteNameResolverInterface $decorated,
        private PathPrefixProviderInterface $pathPrefixProvider,
    ) {
        $this->cacheItemPool = $cacheItemPool;
    }

    public function getRouteName(string $resourceClass, $operationType /*, array $context = []*/): string
    {
        $context = \func_num_args() > 2 ? func_get_arg(2) : [];

        $currentPrefix = sprintf(
            'route_name_%s_',
            (isset($context['section'])) ? $context['section'] : $this->pathPrefixProvider->getCurrentPrefix(),
        );

        $cacheKey = $currentPrefix . md5(
            serialize([$resourceClass, $operationType, $context['subresource_resources'] ?? null]),
        );

        return $this->getCached($cacheKey, function () use ($resourceClass, $operationType, $context) {
            /** @phpstan-ignore-next-line */
            return $this->decorated->getRouteName($resourceClass, $operationType, $context);
        });
    }
}
