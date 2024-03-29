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
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class CustomerDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private readonly string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $docs = $this->updateVerifiedPropertyType($docs);
        $docs = $this->updateCustomerStatisticsExampleResponse($docs);

        return $this->applyCustomerTokenDocumentation($docs);
    }

    private function updateVerifiedPropertyType(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['ShopUser.jsonld-admin.customer.create']['properties']['verified'] = [
            'type' => 'boolean',
            'default' => false,
            'example' => false,
        ];

        $schemas['ShopUser.jsonld-admin.customer.update']['properties']['verified'] = [
            'type' => 'boolean',
            'default' => false,
            'example' => false,
        ];

        return $docs->withComponents($components->withSchemas($schemas));
    }

    private function updateCustomerStatisticsExampleResponse(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Customer-admin.customer.statistics.read'] = [
            'type' => 'object',
            'properties' => [
                'perChannelsStatistics' => [
                    'readOnly' => true,
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'allOrdersCount' => [
                    'readOnly' => true,
                    'type' => 'integer',
                ],
            ],
        ];

        $schemas['Customer.jsonld-admin.customer.statistics.read'] = [
            'type' => 'object',
            'properties' => [
                '@context' => [
                    'readOnly' => true,
                    'oneOf' => [
                        [
                            'type' => 'string',
                        ],
                        [
                            'type' => 'object',
                            'properties' => [
                                '@vocab' => [
                                    'type' => 'string',
                                ],
                                'hydra' => [
                                    'type' => 'string',
                                    'enum' => ['http://www.w3.org/ns/hydra/core#'],
                                ],
                            ],
                            'required' => ['@vocab', 'hydra'],
                            'additionalProperties' => true,
                        ],
                    ],
                ],
                '@id' => [
                    'readOnly' => true,
                    'type' => 'string',
                ],
                '@type' => [
                    'readOnly' => true,
                    'type' => 'string',
                ],
                'perChannelsStatistics' => [
                    'readOnly' => true,
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'allOrdersCount' => [
                    'readOnly' => true,
                    'type' => 'integer',
                ],
            ],
        ];

        $components = $components->withSchemas($schemas);

        return $docs->withComponents($components);
    }

    private function applyCustomerTokenDocumentation(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Customer-shop.customer.token.read'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'customer' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.token.read.unauthorized'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'message' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.token.read.bad-request'] = [
            'type' => 'object',
            'properties' => [
                'type' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'title' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'status' => [
                    'type' => 'integer',
                    'readOnly' => true,
                ],
                'detail' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Customer-shop.customer.token.create'] = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'shop@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'sylius',
                ],
            ],
        ];

        $components = $components->withSchemas($schemas);
        $docs = $docs->withComponents($components);

        $docs->getPaths()->addPath(
            $this->apiRoute . '/shop/customers/token',
            new PathItem(
                post: new Operation(
                    operationId: 'postCustomerCredentialsItem',
                    tags: ['Customer', 'Security'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'JWT token retrieval succeeded',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.token.read',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_UNAUTHORIZED => [
                            'description' => 'JWT token retrieval failed due to invalid credentials',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.token.read.unauthorized',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_BAD_REQUEST => [
                            'description' => 'JWT token retrieval failed due to invalid request',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Customer-shop.customer.token.read.bad-request',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    summary: 'Retrieves the JWT token.',
                    requestBody: new RequestBody(
                        description: 'Retrieves the JWT token.',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Customer-shop.customer.token.create',
                                ],
                            ],
                        ]),
                    ),
                ),
            ),
        );

        return $docs;
    }
}
