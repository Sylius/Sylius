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

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\ImageDocumentationModifier;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;

/**
 * @internal
 *
 * @see ImageNormalizer
 * @see ImageDocumentationModifier
 */
final readonly class ImageFilterAwareResourceMetadataCollectionFactory implements ResourceMetadataCollectionFactoryInterface
{
    private QueryParameter $filterParameter;

    /** @param array<class-string> $imageAwareResources */
    public function __construct(
        private ResourceMetadataCollectionFactoryInterface $decorated,
        private array $imageAwareResources,
    ) {
        $this->filterParameter = new QueryParameter(
            key: ImageNormalizer::FILTER_QUERY_PARAMETER,
            description: 'Image filter to be applied on image resources.',
        );
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = $this->decorated->create($resourceClass);
        if (!$this->isResourceImageRelated($resourceClass)) {
            return $resourceMetadataCollection;
        }

        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            if (null === $operations) {
                continue;
            }

            /** @var Operation $operation */
            foreach ($operations as $name => $operation) {
                if (!$operation instanceof Get && !$operation instanceof GetCollection) {
                    continue;
                }

                $operation = $this->resolveImageFilterParameter($operation);

                $operations->remove($name);
                $operations->add($name, $operation);
            }
        }

        return $resourceMetadataCollection;
    }

    private function resolveImageFilterParameter(Operation $operation): Operation
    {
        $filterParameterKey = $this->filterParameter->getKey();
        $parameters = $operation->getParameters() ?? [];
        if ([] !== $parameters) {
            if ($parameters->has($filterParameterKey)) {
                return $operation;
            }

            $parameters->add($filterParameterKey, $this->filterParameter);
        } else {
            $parameters = [$filterParameterKey => $this->filterParameter];
        }

        return $operation->withParameters($parameters);
    }

    private function isResourceImageRelated(string $resourceClass): bool
    {
        foreach ($this->imageAwareResources as $imageAwareResource) {
            if (is_a($resourceClass, $imageAwareResource, true)) {
                return true;
            }
        }

        return false;
    }
}
