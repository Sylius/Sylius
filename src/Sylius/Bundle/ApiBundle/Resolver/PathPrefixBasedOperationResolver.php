<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\NotExposed;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;

/** @internal */
final class PathPrefixBasedOperationResolver implements OperationResolverInterface
{
    /** @var string[] */
    private array $defaultNamePrefixes = ['_api_/', 'sylius_api_'];

    /** @param iterable<string> $additionalNamePrefixes */
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private readonly iterable $additionalNamePrefixes = []
    ) {
    }

    public function resolve(string $resourceClass, ?string $pathPrefix, ?Operation $operation): ?Operation
    {
        if (
            $operation !== null &&
            $operation->getName() !== '' &&
            !$operation instanceof Patch &&
            !$operation instanceof Put
        ) {
            return $operation;
        }

        $namePrefixes = array_merge($this->defaultNamePrefixes, $this->additionalNamePrefixes);

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            foreach ($resourceMetadata->getOperations() as $operationName => $resourceOperation) {
                if ((!$resourceOperation instanceof Get) && (!$resourceOperation instanceof NotExposed)) {
                    continue;
                }

                foreach ($namePrefixes as $namePrefix) {
                    if (str_starts_with($operationName, $namePrefix . $pathPrefix)) {
                        return $resourceOperation;
                    }
                }
            }
        }

        return $operation;
    }
}
