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

namespace Sylius\Behat\Service\Converter;

use ApiPlatform\Metadata\IriConverterInterface as BaseIriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use ApiPlatform\Metadata\Util\ClassInfoTrait;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;

final readonly class IriConverter implements IriConverterInterface
{
    use ClassInfoTrait;

    public function __construct(
        private BaseIriConverterInterface $decoratedIriConverter,
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
        return $this->decoratedIriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }

    public function getIriFromResourceInSection(
        object|string $resource,
        string $section,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
        ?Operation $operation = null,
        array $context = [],
    ): ?string {
        $resourceClass = $context['force_resource_class'] ?? (\is_string($resource) ? $resource : $this->getObjectClass($resource));
        $operation = $this->operationResolver->resolve($resourceClass, $section, $operation);

        return $this->decoratedIriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }
}
