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

final class ProductSlugDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $path = sprintf('%s/shop/products-by-slug/{slug}', $this->apiRoute);

        $paths = $docs->getPaths();
        $pathItem = $paths->getPath($path);
        $operation = $pathItem?->getGet();
        if (null === $operation) {
            return $docs;
        }

        /** @var Parameter[] $parameters */
        $parameters = $operation->getParameters();

        foreach ($parameters as $key => $parameter) {
            if ($parameter->getName() === 'code') {
                unset($parameters[$key]);
            }
        }

        $operation = $operation->withParameters(array_values($parameters));
        $pathItem = $pathItem->withGet($operation);
        $paths->addPath($path, $pathItem);

        return $docs;
    }
}
