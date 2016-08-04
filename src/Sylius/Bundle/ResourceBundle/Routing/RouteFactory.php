<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RouteFactory implements RouteFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRouteCollection()
    {
        return new RouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute($path, array $defaults = [], array $requirements = [], array $options = [], $host = '', $schemes = [], $methods = [], $condition = '')
    {
        return new Route($path, $defaults, $requirements, $options, $host, $schemes, $methods, $condition);
    }
}
