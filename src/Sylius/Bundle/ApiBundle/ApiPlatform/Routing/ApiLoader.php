<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

final class ApiLoader implements LoaderInterface
{
    /** @param array<array-key, string> $operationsToRemove */
    public function __construct(
        private readonly LoaderInterface $baseApiLoader,
        private readonly array $operationsToRemove,
    ) {
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routeCollection = $this->baseApiLoader->load($resource, $type);
        $routeCollection->remove($this->operationsToRemove);

        return $routeCollection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $this->baseApiLoader->supports($resource, $type);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->baseApiLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->baseApiLoader->setResolver($resolver);
    }
}
