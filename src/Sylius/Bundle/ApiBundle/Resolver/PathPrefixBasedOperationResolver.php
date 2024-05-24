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

namespace Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;

/** @experimental */
final readonly class PathPrefixBasedOperationResolver implements OperationResolverInterface
{
    public function __construct(private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory)
    {
    }

    public function resolve(string $resourceClass, ?string $pathPrefix, ?Operation $operation): ?Operation
    {
        if ($operation !== null && $operation->getName() !== '') {
            return $operation;
        }

        $resourceMetadataCollection = $this->resourceMetadataCollectionFactory->create($resourceClass);
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            foreach ($resourceMetadata->getOperations() as $operationName => $resourceOperation) {
                if ($resourceOperation instanceof CollectionOperationInterface) {
                    continue;
                }

                if (str_starts_with($operationName, '_api_/' . $pathPrefix)) {
                    return $resourceOperation;
                }
            }
        }

        return $operation;
    }
}
