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

namespace spec\Sylius\Bundle\UiBundle\Twig;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\ExtensionInterface;

final class RouteExistsExtensionSpec extends ObjectBehavior
{
    function let(RouterInterface $router): void
    {
        $this->beConstructedWith($router);
    }

    function it_is_twig_extension(): void
    {
        $this->shouldImplement(ExtensionInterface::class);
    }

    function it_returns_true_if_route_exists(
        RouterInterface $router,
        RouteCollection $routeCollection,
    ): void {
        $route = new Route('/products/{id}');
        $router->getRouteCollection()->willReturn($routeCollection);

        $routeCollection->get('sylius_admin_product_show')->willReturn($route);

        $this->routeExists('sylius_admin_product_show')->shouldReturn(true);
    }

    function it_returns_false_if_route_does_not_exist(
        RouterInterface $router,
        RouteCollection $routeCollection,
    ): void {
        $router->getRouteCollection()->willReturn($routeCollection);

        $routeCollection->get('sylius_admin_product_attribute_show')->willReturn(null);

        $this->routeExists('sylius_admin_product_attribute_show')->shouldReturn(false);
    }
}
