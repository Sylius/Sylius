<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Factory;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Extractor\ExtractorInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use Sylius\Bundle\ApiBundle\ApiPlatform\ResourceMetadataPropertyValueResolver;

/**
 * @experimental
 * This class is overwriting ApiPlatform ExtractorResourceMetadataFactory to allow yaml files to be merged into api platform config
 */
final class MergingExtractorResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    /** @var ExtractorInterface */
    private $extractor;

    /** @var ResourceMetadataFactoryInterface */
    private $decorated;

    /** @var ResourceMetadataPropertyValueResolver */
    private $resourceMetadataPropertyValueResolver;

    /** @var array */
    private $defaults;

    /** @var array */
    private const RESOURCES = ['shortName', 'description', 'iri', 'itemOperations', 'collectionOperations', 'subresourceOperations', 'graphql', 'attributes'];

    public function __construct(
        ExtractorInterface $extractor,
        ResourceMetadataFactoryInterface $decorated,
        ResourceMetadataPropertyValueResolver  $resourceMetadataPropertyValueResolver,
        array $defaults = []
    ) {
        $this->extractor = $extractor;
        $this->decorated = $decorated;
        $this->resourceMetadataPropertyValueResolver = $resourceMetadataPropertyValueResolver;
        $this->defaults = $defaults + ['attributes' => []];
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass): ResourceMetadata
    {
        $parentResourceMetadata = null;
        if ($this->decorated) {
            try {
                $parentResourceMetadata = $this->decorated->create($resourceClass);
            } catch (ResourceClassNotFoundException $resourceNotFoundException) {
                // Ignore not found exception from decorated factories
            }
        }

        if (!(class_exists($resourceClass) || interface_exists($resourceClass)) || !$resource = $this->extractor->getResources()[$resourceClass] ?? false) {
            return $this->handleNotFound($parentResourceMetadata, $resourceClass);
        }

        foreach (self::RESOURCES as $availableResource) {
            $resource[$availableResource] =
                $resource[$availableResource] ?? $this->defaults[strtolower(preg_replace('/(?<!^)[A-Z]+|(?<!^|\d)[\d]+/', '_$0', $availableResource))] ?? null
            ;
        }

        if ($resource['attributes'] ==! null || !empty($this->defaults['attributes'])) {
            $resource['attributes'] = (array) $resource['attributes'];
            foreach ($this->defaults['attributes'] as $key => $value) {
                if (!isset($resource['attributes'][$key])) {
                    $resource['attributes'][$key] = $value;
                }
            }
        }

        return $this->update($parentResourceMetadata ?? new ResourceMetadata(), $resource);
    }

    /**
     * Returns the metadata from the decorated factory if available or throws an exception.
     *
     * @throws ResourceClassNotFoundException
     */
    private function handleNotFound(?ResourceMetadata $parentPropertyMetadata, string $resourceClass): ResourceMetadata
    {
        if (null !== $parentPropertyMetadata) {
            return $parentPropertyMetadata;
        }

        throw new ResourceClassNotFoundException(sprintf('Resource "%s" not found.', $resourceClass));
    }

    /**
     * Creates a new instance of metadata if the property is not already set.
     */
    private function update(ResourceMetadata $resourceMetadata, array $metadata): ResourceMetadata
    {
        foreach (self::RESOURCES as $propertyName) {
            $propertyValue = $this->resourceMetadataPropertyValueResolver->resolve($propertyName, $resourceMetadata, $metadata);
            if (null !== $propertyValue) {
                $resourceMetadata = $resourceMetadata->{'with' . ucfirst($propertyName)}($propertyValue);
            }
        }

        return $resourceMetadata;
    }
}
