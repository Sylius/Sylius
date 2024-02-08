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

use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RedirectPathExtension extends AbstractExtension
{
    private const NUMBER_OF_ROUTE_PROPERTIES = 3;

    public function __construct(
        private FilterStorageInterface $filterStorage,
        private RouterInterface $router,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_generate_redirect_path', [$this, 'generateRedirectPath']),
        ];
    }

    public function generateRedirectPath(?string $path): ?string
    {
        if (null === $path) {
            return null;
        }

        $request = Request::create($path);

        try {
            $routeInfo = $this->router->match($request->getPathInfo());
        } catch (\Throwable) {
            return $path;
        }

        if ([] !== $request->query->all() || $this->hasAdditionalParameters($routeInfo)) {
            return $path;
        }

        $route = $routeInfo['_route'];

        return $this->router->generate($route, $this->filterStorage->all());
    }

    private function hasAdditionalParameters(array $routeInfo): bool
    {
        return count($routeInfo) > self::NUMBER_OF_ROUTE_PROPERTIES;
    }
}
