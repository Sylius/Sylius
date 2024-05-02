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
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Symfony\Component\HttpFoundation\Response;

final class StatisticsDocumentationModifier implements DocumentationModifierInterface
{
    /** @param array<string, array<string, string>> $intervalsMap */
    public function __construct(
        private string $apiRoute,
        private DateTimeProviderInterface $dateTimeProvider,
        private array $intervalsMap,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $schemas = $docs->getComponents()->getSchemas();
        $schemas['Statistics'] = [
            'type' => 'object',
            'properties' => [
                'sales' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'period' => [
                                'type' => 'string',
                                'format' => 'date-time',
                                'example' => '1999-12',
                            ],
                            'total' => [
                                'type' => 'integer',
                                'example' => 1000,
                            ],
                        ],
                    ],
                ],
                'businessActivitySummary' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'totalSales' => [
                                'type' => 'integer',
                                'example' => 100000,
                            ],
                            'paidOrdersCount' => [
                                'type' => 'integer',
                                'example' => 12,
                            ],
                            'newCustomersCount' => [
                                'type' => 'integer',
                                'example' => 7,
                            ],
                            'averageOrderValue' => [
                                'type' => 'integer',
                                'example' => 2500,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $path = sprintf('%s/admin/statistics', $this->apiRoute);
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
            ref: 'Statistics',
            summary: 'Get statistics',
            get: new Operation(
                operationId: 'get_statistics',
                tags: ['Statistics'],
                responses: [
                    Response::HTTP_OK => [
                        'description' => 'Statistics',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Statistics',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Get statistics',
                description: 'Get statistics',
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

        $startDate = new Parameter(
            name: 'startDate',
            in: 'query',
            description: 'Start date for statistics',
            required: true,
            schema: [
                'type' => 'string',
                'format' => 'date-time',
                'default' => $this->dateTimeProvider->now()->format('Y-01-01\T00:00:00'),
            ],
        );

        $interval = new Parameter(
            name: 'interval',
            in: 'query',
            description: 'Interval type for statistics',
            required: true,
            schema: [
                'type' => 'string',
                'default' => 'month',
                'enum' => array_keys($this->intervalsMap),
            ],
        );

        $endDate = new Parameter(
            name: 'endDate',
            in: 'query',
            description: 'End date for statistics',
            required: true,
            schema: [
                'type' => 'string',
                'format' => 'date-time',
                'default' => $this->dateTimeProvider->now()->format('Y-12-31\T23:59:59'),
            ],
        );

        return [$channelCode, $startDate, $interval, $endDate];
    }
}
