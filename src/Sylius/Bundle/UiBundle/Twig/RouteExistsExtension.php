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

namespace Sylius\Bundle\UiBundle\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RouteExistsExtension extends AbstractExtension
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_route_exists', [$this, 'routeExists']),
        ];
    }

    /**
     * @throws \Exception
     */
    public function routeExists(string $routeName): bool
    {
        $routeCollection = $this->router->getRouteCollection();

        return $routeCollection->get($routeName) !== null;
    }
}
