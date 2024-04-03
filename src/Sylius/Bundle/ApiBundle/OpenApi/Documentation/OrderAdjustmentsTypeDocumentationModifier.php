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

use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\OpenApi;

final class OrderAdjustmentsTypeDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private string $apiRoute, private string $adjustmentResourceClass)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $path = sprintf('%s/admin/orders/{tokenValue}/adjustments', $this->apiRoute);

        $paths = $docs->getPaths();
        $pathItem = $paths->getPath($path);
        $operation = $pathItem?->getGet();
        if (null === $operation) {
            return $docs;
        }

        $parameters = $operation->getParameters();
        $parameters[] = new Parameter(
            name: 'type',
            in: 'query',
            description: 'Type of adjustments you want to get',
            schema: [
                'type' => 'string',
                'enum' => call_user_func([$this->adjustmentResourceClass, 'getAdjustmentTypeChoices']),
                'nullable' => true,
                'default' => null,
            ],
        );

        $operation = $operation->withParameters($parameters);
        $pathItem = $pathItem->withGet($operation);
        $paths->addPath($path, $pathItem);

        return $docs->withPaths($paths);
    }
}
