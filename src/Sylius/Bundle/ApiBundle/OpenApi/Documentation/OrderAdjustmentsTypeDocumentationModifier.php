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

final readonly class OrderAdjustmentsTypeDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(
        private string $apiRoute,
        private string $adjustmentResourceClass,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $pathsToBeUpdated = [
            sprintf('%s/admin/orders/{tokenValue}/adjustments', $this->apiRoute),
            sprintf('%s/admin/order-items/{id}/adjustments', $this->apiRoute),
            sprintf('%s/shop/orders/{tokenValue}/adjustments', $this->apiRoute),
            sprintf('%s/shop/orders/{tokenValue}/items/{id}/adjustments', $this->apiRoute),
        ];
        $documentationPaths = $docs->getPaths();

        foreach ($pathsToBeUpdated as $path) {
            $pathItem = $this->extendPathGetOperationWithAdjustmentTypeEnum($documentationPaths->getPath($path));
            if (null === $pathItem) {
                continue;
            }

            $documentationPaths->addPath($path, $pathItem);
        }

        return $docs->withPaths($documentationPaths);
    }

    private function extendPathGetOperationWithAdjustmentTypeEnum(?PathItem $pathItem): ?PathItem
    {
        $operation = $pathItem?->getGet();
        if (null === $operation) {
            return null;
        }

        /** @var Parameter[] $parameters */
        $parameters = $operation->getParameters();
        foreach ($parameters as &$parameter) {
            if ($parameter->getIn() === 'query' && $parameter->getName() === 'type') {
                $schema = $parameter->getSchema();
                $schema['enum'] = call_user_func([$this->adjustmentResourceClass, 'getAdjustmentTypeChoices']);
                $parameter = $parameter->withSchema($schema);
            }
        }

        $operation = $operation->withParameters($parameters);

        return $pathItem->withGet($operation);
    }
}
