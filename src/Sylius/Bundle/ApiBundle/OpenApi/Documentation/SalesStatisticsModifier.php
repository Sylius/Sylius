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

use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
final class SalesStatisticsModifier implements DocumentationModifierInterface
{
    private const PATH = '/admin/sales-statistics';

    public function __construct(private string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $schemas = $docs->getComponents()->getSchemas();
        $schemas['SalesStatistics'] = [
            'type' => 'object',
            'properties' => [
                'salesPerPeriod' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'period' => [
                                'type' => 'string',
                                'format' => 'date-time',
                            ],
                            'total' => [
                                'type' => 'integer',
                                'example' => '1000',
                            ],
                        ],
                    ],
                    'minItems' => 12,
                ],
                'newCustomersCount' => [
                    'type' => 'integer',
                    'example' => '10',
                ],
                'newOrdersCount' => [
                    'type' => 'integer',
                    'example' => '12',
                ],
                'averageOrderValue' => [
                    'type' => 'integer',
                    'example' => '1000',
                ],
                'totalSales' => [
                    'type' => 'integer',
                    'example' => '12000',
                ],
                'intervalType' => [
                    'type' => 'string',
                    'enum' => ['month', 'year', 'week', 'day'],
                ],
            ],
        ];

        $path = $this->apiRoute . self::PATH;
        $paths = $docs->getPaths();
        $paths->addPath($path, $this->getPathItem());

        return $docs
            ->withPaths($paths)
            ->withComponents($docs->getComponents()->withSchemas($schemas))
        ;
    }

    private function getPathItem(): PathItem
    {
        return new PathItem(
            ref: 'Sales statistics',
            summary: 'Get sales statistics',
            get: new Operation(
                operationId: 'get_sales_statistics',
                tags: ['Sales statistics'],
                responses: [
                    Response::HTTP_OK => [
                        'description' => 'Sales statistics',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/SalesStatistics',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get sales statistics',
                description: 'Get sales statistics',
                parameters: $this->getParameters(),
            ),
        );
    }

    /** @return Parameter[] */
    private function getParameters(): array
    {
        $channelCode = new Parameter(
            name: 'channelCode',
            in: 'query',
            description: 'Channel to get statistics for',
            required: true,
            schema: [
                'type' => 'string',
            ],
        );

        return [$channelCode];
    }
}
