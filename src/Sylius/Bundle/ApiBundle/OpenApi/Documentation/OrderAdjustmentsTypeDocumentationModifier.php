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

/** @experimental */
final class OrderAdjustmentsTypeDocumentationModifier implements DocumentationModifierInterface
{
    public const PATH = '%s/admin/orders/{tokenValue}/adjustments';

    public function __construct(private string $apiRoute, private string $adjustmentResourceClass)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $paths = $docs->getPaths();

        $path = sprintf(self::PATH, $this->apiRoute);
        $pathItem = $paths->getPath($path);
        $operation = $pathItem->getGet();

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
