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

/** @experimental */
final class ShopAuthenticationTokenDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(private string $apiRoute)
    {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['ShopUserToken'] = [
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

        $schemas['ShopUserCredentials'] = [
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
            $this->apiRoute . '/shop/authentication-token',
            new PathItem(
                post: new Operation(
                    operationId: 'postCredentialsItem',
                    tags: ['ShopUserToken'],
                    responses: [
                        Response::HTTP_OK => [
                            'description' => 'Get JWT token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ShopUserToken',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    summary: 'Get JWT token to login.',
                    requestBody: new RequestBody(
                        description: 'Create new JWT Token',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/ShopUserCredentials',
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
