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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;
use Webmozart\Assert\Assert;

final class LocalizedRoutesLoader implements LoaderInterface
{
    /**
     * @param array<string, array<string, string>> $localizedRoutes
     */
    public function __construct(
        private readonly LoaderInterface $decoratedLoader,
        private readonly array $localizedRoutes,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): mixed
    {
        $routes = $this->decoratedLoader->load($resource, $type);
        Assert::isInstanceOf($routes, RouteCollection::class);

        $allRoutes = $routes->all();

        foreach (array_keys($this->localizedRoutes) as $name) {
            Assert::keyExists($allRoutes, $name, sprintf(
                'Route "%s" cannot be localized, as it is not defined. Check the "sylius_core.localized_routes" configuration.',
                $name,
            ));
        }

        // Every route is added again so the localized routes keep the position of the one they replace, as route order decides matching.
        foreach ($allRoutes as $name => $route) {
            $priority = $routes->getPriority($name) ?? 0;

            if (!isset($this->localizedRoutes[$name])) {
                $routes->add($name, $route, $priority);

                continue;
            }

            $routes->remove($name);

            foreach ($this->localizedRoutes[$name] as $locale => $path) {
                $localizedRoute = clone $route;
                $localizedRoute->setPath($path);
                $localizedRoute->setDefault('_locale', $locale);
                $localizedRoute->setRequirement('_locale', preg_quote($locale));
                $localizedRoute->setDefault('_canonical_route', $name);

                $routes->add(sprintf('%s.%s', $name, $locale), $localizedRoute, $priority);
            }
        }

        return $routes;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $this->decoratedLoader->supports($resource, $type);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->decoratedLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->decoratedLoader->setResolver($resolver);
    }
}
