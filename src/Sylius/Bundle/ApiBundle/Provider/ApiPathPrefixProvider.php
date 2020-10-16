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

namespace Sylius\Bundle\ApiBundle\Provider;

/** @experimental */
final class ApiPathPrefixProvider implements ApiPathPrefixProviderInterface
{
    const SHOP_PREFIX = 'shop';

    const ADMIN_PREFIX = 'admin';

    /** @var string */
    private $apiRoute;

    public function __construct(string $apiRoute)
    {
        $this->apiRoute = $apiRoute;
    }

    public function getPathPrefix(string $path): ?string
    {
        $pathElements = array_values(array_filter(explode('/', $path)));

        if ('/' . $pathElements[0] !== $this->apiRoute) {
            return null;
        }

        if ($pathElements[1] === self::SHOP_PREFIX) {
            return self::SHOP_PREFIX;
        }

        if ($pathElements[1] === self::ADMIN_PREFIX) {
            return self::ADMIN_PREFIX;
        }

        return null;
    }
}
