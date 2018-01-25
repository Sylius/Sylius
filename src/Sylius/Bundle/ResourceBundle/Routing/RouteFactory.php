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

namespace Sylius\Bundle\ResourceBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouteFactory implements RouteFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute(
        string $path,
        array $defaults = [],
        array $requirements = [],
        array $options = [],
        string $host = '',
        array $schemes = [],
        array $methods = [],
        string $condition = ''
    ): Route {
        return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }
}
