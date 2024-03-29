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

final class AdministratorDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private readonly string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Administrator-admin.administrator.token.read'] = [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'adminUser' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $schemas['Administrator-admin.administrator.token.read.unauthorized'] = [
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

        $schemas['Administrator-admin.administrator.token.read.bad-request'] = [
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

        $schemas['Administrator-admin.administrator.token.create'] = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'api@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'sylius-api',
                ],
            ],
        ];

        $components = $components->withSchemas($schemas);
        $docs = $docs->withComponents($components);

        $docs->getPaths()->addPath(
            $this->apiRoute . '/admin/administrators/token',
            new PathItem(
                post: new Operation(
                    operationId: 'postAdministratorCredentialsItem',
                    tags: ['Administrator', 'Security'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'JWT token retrieval succeeded',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Administrator-admin.administrator.token.read',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_UNAUTHORIZED => [
                            'description' => 'JWT token retrieval failed due to invalid credentials',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Administrator-admin.administrator.token.read.unauthorized',
                                    ],
                                ],
                            ],
                        ],
                        Response::HTTP_BAD_REQUEST => [
                            'description' => 'JWT token retrieval failed due to invalid request',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Administrator-admin.administrator.token.read.bad-request',
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
                                    '$ref' => '#/components/schemas/Administrator-admin.administrator.token.create',
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
