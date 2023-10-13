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
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;

/** @experimental */
final readonly class PathPrefixBasedOperationResolver implements OperationResolverInterface
{
    public function __construct(
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private PathPrefixProviderInterface $pathPrefixProvider,
    ) {
    }

    public function resolve(string $resourceClass, string $requestUri, ?Operation $operation): Operation
    {
        if ($operation !== null && $operation->getName() !== '') {
            return $operation;
        }

        $pathPrefix = $this->pathPrefixProvider->getPathPrefix($requestUri);

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
