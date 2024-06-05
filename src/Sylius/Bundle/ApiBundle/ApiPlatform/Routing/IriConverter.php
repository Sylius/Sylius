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

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Api\UrlGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;

final readonly class IriConverter implements IriConverterInterface
{
    use ClassInfoTrait;

    public function __construct(
        private IriConverterInterface $decoratedIriConverter,
        private PathPrefixProviderInterface $pathPrefixProvider,
        private OperationResolverInterface $operationResolver,
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
        $resourceClass = $context['force_resource_class'] ?? (\is_string($resource) ? $resource : $this->getObjectClass($resource));
        $pathPrefix = $this->pathPrefixProvider->getPathPrefix($context['request_uri'] ?? '');
        $operation = $this->operationResolver->resolve($resourceClass, $pathPrefix, $operation);

        return $this->decoratedIriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }
}
