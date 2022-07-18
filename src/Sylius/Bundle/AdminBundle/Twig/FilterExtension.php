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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Bundle\AdminBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class FilterExtension extends AbstractExtension
{
    public function __construct(
        private FilterStorageInterface $filterStorage,
        private RouterInterface $router,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_generate_path', [$this, 'generatePath']),
        ];
    }

    public function generatePath(string $path): string
    {
        $request = Request::create($path);

        try {
            $routeInfo = $this->router->match($request->getPathInfo());
        } catch (\Throwable) {
            return $path;
        }

        if ([] !== $request->query->all()) {
            return $path;
        }

        $route = $routeInfo['_route'];

        return $this->router->generate($route, $this->filterStorage->all());
    }
}
