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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Routing;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\ResourceClassResolverInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class IriConverter implements IriConverterInterface
{
    use ClassInfoTrait;

    public function __construct(
        private IriConverterInterface $decoratedIriConverter,
        private PathPrefixProviderInterface $pathPrefixProvider,
        private OperationResolverInterface $operationResolver,
        private RouterInterface $router,
        private ResourceClassResolverInterface $resourceClassResolver,
    ) {
    }

    public function getResourceFromIri(string $iri, array $context = [], ?Operation $operation = null): object
    {
        return $this->decoratedIriConverter->getResourceFromIri($iri, $context, $operation);
    }

    public function getIriFromResource(
        object|string $resource,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
        ?Operation $operation = null,
        array $context = [],
    ): ?string {
        if (isset($context['force_resource_class'])) {
            $resourceClass = $context['force_resource_class'];
        } elseif (\is_string($resource)) {
            $resourceClass = $resource;
        } else {
            $objectClass = $this->getObjectClass($resource);
            $resourceClass = $this->resourceClassResolver->isResourceClass($objectClass)
                ? $this->resourceClassResolver->getResourceClass($resource)
                : $objectClass;
        }
        $pathPrefix = $this->pathPrefixProvider->getPathPrefix($context['request_uri'] ?? $this->router->getContext()->getPathInfo());
        $operation = $this->operationResolver->resolve($resourceClass, $pathPrefix, $operation);

        return $this->decoratedIriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }
}
