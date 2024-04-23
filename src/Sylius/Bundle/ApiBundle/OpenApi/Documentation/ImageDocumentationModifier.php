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

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer;

final class ImageDocumentationModifier implements DocumentationModifierInterface
{
    /** @var array<string> */
    private array $filters;

    public function __construct(
        ImageFiltersProviderInterface $filterProvider,
    ) {
        $this->filters = $filterProvider->getFilters();
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $documentationPaths = $docs->getPaths();

        /**
         * @param string $path
         * @param PathItem $pathItem
         */
        foreach ($documentationPaths->getPaths() as $path => $pathItem) {
            $pathItem = $this->extendPathGetOperationWithFiltersEnum($pathItem, $this->filters);
            if (null === $pathItem) {
                continue;
            }

            $documentationPaths->addPath($path, $pathItem);
        }

        return $docs;
    }

    /** @param array<string> $filters */
    private function extendPathGetOperationWithFiltersEnum(PathItem $pathItem, array $filters): ?PathItem
    {
        $operation = $pathItem->getGet();
        if (null === $operation) {
            return null;
        }

        /** @var Parameter[] $parameters */
        $parameters = $operation->getParameters();
        foreach ($parameters as &$parameter) {
            if ($parameter->getIn() === 'query' && $parameter->getName() === ImageNormalizer::FILTER_QUERY_PARAMETER) {
                $schema = $parameter->getSchema();
                $schema['enum'] = $filters;
                $parameter = $parameter->withSchema($schema);
            }
        }

        $operation = $operation->withParameters($parameters);

        return $pathItem->withGet($operation);
    }
}
