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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidIdentifierException;
use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Identifier\IdentifierConverterInterface;

/** @internal */
trait OperationDataProviderTrait
{
    /**
     * @var CollectionDataProviderInterface
     */
    private $collectionDataProvider;

    /**
     * @var ItemDataProviderInterface
     */
    private $itemDataProvider;

    /**
     * @var SubresourceDataProviderInterface|null
     */
    private $subresourceDataProvider;

    /**
     * @var IdentifierConverterInterface|null
     */
    private $identifierConverter;

    /**
     * Retrieves data for a collection operation.
     *
     * @return iterable
     */
    private function getCollectionData(array $attributes, array $context)
    {
        return $this->collectionDataProvider->getCollection($attributes['resource_class'], $attributes['collection_operation_name'], $context);
    }

    /**
     * Gets data for an item operation.
     *
     * @return object|null
     */
    private function getItemData($identifiers, array $attributes, array $context)
    {
        return $this->itemDataProvider->getItem($attributes['resource_class'], $identifiers, $attributes['item_operation_name'], $context);
    }

    /**
     * Gets data for a nested operation.
     *
     * @throws RuntimeException
     *
     * @return array|object|null
     */
    private function getSubresourceData($identifiers, array $attributes, array $context)
    {
        if (null === $this->subresourceDataProvider) {
            throw new RuntimeException('Subresources not supported');
        }

        return $this->subresourceDataProvider->getSubresource($attributes['resource_class'], $identifiers, $attributes['subresource_context'] + $context, $attributes['subresource_operation_name']);
    }

    /**
     * @param array $parameters - usually comes from $request->attributes->all()
     *
     * @throws InvalidIdentifierException
     */
    private function extractIdentifiers(array $parameters, array $attributes)
    {
        if (isset($attributes['item_operation_name'])) {
            $identifier = $this->provideIdentifier($parameters);
            if ($identifier === null) {
                throw new InvalidIdentifierException('Identifier not found');
            }

            if (null !== $this->identifierConverter) {
                return $this->identifierConverter->convert((string) $identifier, $attributes['resource_class']);
            }

            return $identifier;
        }

        if (!isset($attributes['subresource_context'])) {
            throw new RuntimeException('Either "item_operation_name" or "collection_operation_name" must be defined, unless the "_api_receive" request attribute is set to false.');
        }

        $identifiers = [];

        foreach ($attributes['subresource_context']['identifiers'] as $key => [$id, $resourceClass, $hasIdentifier]) {
            if (false === $hasIdentifier) {
                continue;
            }

            if (!isset($parameters[$id])) {
                $identifier = $this->provideIdentifier($parameters);
                if ($identifier === null) {
                    throw new InvalidIdentifierException('Identifier not found');
                }
                $identifiers[$id] = $identifier;
            } else {
                $identifiers[$id] = $parameters[$id];
            }

            if (null !== $this->identifierConverter) {
                $identifiers[$id] = $this->identifierConverter->convert((string) $identifiers[$id], $resourceClass);
            }
        }

        return $identifiers;
    }

        private function provideIdentifier(array $parameters)
        {
            $identifier = null;

            if (isset($parameters['id'])) {
                return $parameters['id'];
            }

            if (isset($parameters['code'])) {
                return $parameters['code'];
            }

            if (isset($parameters['tokenValue'])) {
                return $parameters['tokenValue'];
            }

            return null;
        }
}
