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
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
final class SalesStatisticsModifier implements DocumentationModifierInterface
{
    public function __construct(private string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['SalesStatistics'] = [
            'type' => 'object',
            'properties' => [
                'salesInPeriod' => [
                    [
                        'period' => '2023-01-01 00:00:00',
                        'total' => 0,
                    ],
                    [
                        'period' => '2023-02-01 00:00:00',
                        'total' => 0,
                    ],
                    [
                        'period' => '2023-03-01 00:00:00',
                        'total' => 0,
                    ],
                    [
                        'period' => '2023-04-01 00:00:00',
                        'total' => 0,
                    ],
                    [
                        'period' => '2023-05-01 00:00:00',
                        'total' => 0,
                    ],
                    // The rest of the months are omitted for brevity.
                    [
                        'period' => '2023-12-01 00:00:00',
                        'total' => 19500,
                    ],
                ],
                'totalSales' => 19500,
                'newCustomersCount' => 4,
                'newOrdersCount' => 3,
                'averageOrderValue' => 6500,
                'intervalType' => 'month',
            ],
        ];

        $components = $components->withSchemas($schemas);
        $docs = $docs->withComponents($components);

        $docs->getPaths()->addPath(
            $this->apiRoute . '/admin/sales-statistics',
            new PathItem(
                get: new Operation(
                    operationId: 'postCredentialsItem',
                    tags: ['DashboardStatistics'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'Get sales statistics data.',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/SalesStatistics',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    summary: 'Get sales statistics data.',
                ),
            ),
        );

        return $docs;
    }
}
