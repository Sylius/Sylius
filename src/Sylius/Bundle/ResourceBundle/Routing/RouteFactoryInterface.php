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

interface RouteFactoryInterface
{
    /**
     * @return RouteCollection
     */
    public function createRouteCollection();

    /**
     * @param string $path The path pattern to match
     * @param array $defaults An array of default parameter values
     * @param array $requirements An array of requirements for parameters (regexes)
     * @param array $options An array of options
     * @param string $host The host pattern to match
     * @param array $schemes An array of restricted URI schemes
     * @param array $methods An array of restricted HTTP methods
     * @param string $condition A condition that should evaluate to true for the route to match
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
    ): Route;
}
