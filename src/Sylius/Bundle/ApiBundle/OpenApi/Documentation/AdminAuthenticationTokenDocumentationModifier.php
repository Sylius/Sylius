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

final class AdminAuthenticationTokenDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private readonly string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Administrator-admin.authentication-token.read'] = [
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

        $schemas['Administrator-admin.authentication-token.create'] = [
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
            $this->apiRoute . '/admin/authentication-token',
            new PathItem(
                post: new Operation(
                    operationId: 'postCredentialsItem',
                    tags: ['Administrator'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'JWT token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Administrator-admin.authentication-token.read',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    summary: 'Retrieves the JWT token to login.',
                    requestBody: new RequestBody(
                        description: 'Retrieves the JWT token to login.',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Administrator-admin.authentication-token.create',
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
