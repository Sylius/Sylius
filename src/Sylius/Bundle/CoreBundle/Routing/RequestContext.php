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

namespace Sylius\Bundle\CoreBundle\Routing;

use Symfony\Component\Routing\RequestContext as BaseRequestContext;

final class RequestContext extends BaseRequestContext
{
    /**
     * @param array{enabled: bool, routes: array<string, array{enabled: bool}>} $bcLayerConfig
     */
    public function __construct(
        BaseRequestContext $decorated,
        private readonly array $bcLayerConfig,
    ) {
        parent::__construct(
            $decorated->getBaseUrl(),
            $decorated->getMethod(),
            $decorated->getHost(),
            $decorated->getScheme(),
            $decorated->getHttpPort(),
            $decorated->getHttpsPort(),
            $decorated->getPathInfo(),
            $decorated->getQueryString()
        );
    }

    public function isSyliusRoutingBcLayerEnabled(string $key): bool
    {
        return $this->bcLayerConfig['routes'][$key]['enabled'] ?? $this->bcLayerConfig['enabled'] ?? true;

    }
}
