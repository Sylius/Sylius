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
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Bundle\ApiBundle\Provider\ProductImageFilterProviderInterface;

final class ProductImageDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(
        private ProductImageFilterProviderInterface $filterProvider,
        private string $apiRoute,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $enums = array_keys($this->filterProvider->provideShopFilters());

        $path = sprintf('%s/shop/product-images/{id}', $this->apiRoute);

        $paths = $docs->getPaths();
        $pathItem = $paths->getPath($path);
        $operation = $pathItem?->getGet();

        if (null === $operation) {
            return $docs;
        }

        /** @var Parameter[] $parameters */
        $parameters = $operation->getParameters();

        foreach ($parameters as &$parameter) {
            if ($parameter->getIn() === 'query' && $parameter->getName() === 'filter') {
                $schema = $parameter->getSchema();
                $schema['enum'] = $enums;
                $parameter = $parameter->withSchema($schema);
            }
        }

        $operation = $operation->withParameters($parameters);
        $pathItem = $pathItem->withGet($operation);
        $paths->addPath($path, $pathItem);

        return $docs;
    }
}
