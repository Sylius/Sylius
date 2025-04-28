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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ApiPlatform\Metadata\WithResourceTrait;

/**
 * @internal
 *
 * This class is used to merge duplicated operations with the same name in the resource metadata collection.
 *
 * Exemple with an input class replaced:
 * Before entering this class:
 *     ResourceMetadataCollection
 *     ├── ResourceMetadata (XML file defining an API resource in Sylius vendors)
 *     │   ├── Operations
 *     │   │   ├── Operation sylius_shop_foo_post (POST /api/v2/shop/foo with input: FooInput)
 *     ├── ResourceMetadata (another XML file defining the same API resource in the app directory)
 *     │   ├── Operations
 *     │   │   ├── Operation sylius_shop_foo_post (POST /api/v2/shop/foo with input: BarInput)
 *     │   │   ├── Operation app_shop_custom_get (GET /api/v2/shop/custom)
 * After entering this class:
 *    ResourceMetadataCollection
 *      ├── ResourceMetadata (XML file defining an API resource in Sylius vendors)
 *      │   ├── Operations
 *      │   │   ├── Operation sylius_shop_foo_post (POST /api/v2/shop/foo with input: BarInput)
 *      ├── ResourceMetadata (another XML file defining the same API resource in the app directory)
 *      │   ├── Operations
 *      │   │   ├── Operation app_shop_custom_get (GET /api/v2/shop/custom)
 */
final class DuplicateOperationReplacerResourceMetadataCollectionFactory implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $decorated,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        /** @var array<string, bool> $duplicatedOperationNames */
        $duplicatedOperationNames = [];

        foreach ($resourceMetadataCollection as $key => $resourceMetadata) {
            if ($resourceMetadata->getOperations()) {
                $resourceMetadata = $resourceMetadata->withOperations(
                    $this->getTransformedOperations(
                        $resourceMetadata->getOperations(),
                        $resourceMetadataCollection,
                        $duplicatedOperationNames,
                    ),
                );
            }

            if ($resourceMetadata->getGraphQlOperations()) {
                $resourceMetadata = $resourceMetadata->withGraphQlOperations(
                    $this->getTransformedOperations(
                        $resourceMetadata->getGraphQlOperations(),
                        $resourceMetadataCollection,
                        $duplicatedOperationNames,
                    ),
                );
            }

            $resourceMetadataCollection[$key] = $resourceMetadata;
        }

        return $resourceMetadataCollection;
    }

    /**
     * @param Operations<Operation>|Operation[] $operations
     * @param array<string, bool> $duplicatedOperationNames
     *
     * @return Operations<Operation>|Operation[]
     */
    private function getTransformedOperations(
        array|Operations $operations,
        ResourceMetadataCollection $resourceMetadataCollection,
        array &$duplicatedOperationNames,
    ): array|Operations {
        foreach ($operations as $name => $operation) {
            if (isset($duplicatedOperationNames[$name])) {
                if ($operations instanceof Operations) {
                    $operations->remove($name);
                } else {
                    unset($operations[$name]);
                }

                continue;
            }

            $duplicatedOperationNames[$name] = true;

            foreach ($this->findOperations(
                $resourceMetadataCollection,
                $name,
                $operation,
                false === $operations instanceof Operations,
            ) as $duplicatedOperation) {
                $operation = $this->copyFrom($operation, $duplicatedOperation);

                if ($operations instanceof Operations) {
                    $operations->add($name, $operation);
                } else {
                    $operations[$name] = $operation;
                }
            }
        }

        return $operations;
    }

    /**
     * @return iterable<Operation>
     */
    private function findOperations(
        ResourceMetadataCollection $resourceMetadataCollection,
        string $key,
        Operation $currentOperation,
        bool $isGraphQl,
    ): iterable {
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $method = $isGraphQl ? 'getGraphQlOperations' : 'getOperations';
            foreach ($resourceMetadata->$method() as $name => $operation) {
                if ($name !== $key) {
                    continue;
                }

                if ($currentOperation === $operation) {
                    continue;
                }

                yield $operation;
            }
        }
    }

    /**
     * This method copy properties from $newOperation to $operation by replicating what is done in:
     *
     * @see WithResourceTrait::copyFrom
     * Changes applied:
     *   - The null test on the $operation has been removed.
     */
    private function copyFrom(Operation $operation, Operation $newOperation): Operation
    {
        $self = clone $operation;
        foreach (get_class_methods($newOperation) as $method) {
            if (
                method_exists($self, $method) &&
                preg_match('/^(?:get|is|can)(.*)/', (string) $method, $matches) &&
                null !== $val = $newOperation->{$method}()
            ) {
                $self = $self->{"with{$matches[1]}"}($val);
            }
        }

        return $self->withExtraProperties(array_merge($newOperation->getExtraProperties(), $self->getExtraProperties()));
    }
}
