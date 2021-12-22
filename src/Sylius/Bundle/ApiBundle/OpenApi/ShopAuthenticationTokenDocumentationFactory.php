<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

/** @experimental */
final class ShopAuthenticationTokenDocumentationFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decoratedFactory;

    private string $apiRoute;

    public function __construct(OpenApiFactoryInterface $decoratedFactory, string $apiRoute)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->apiRoute = $apiRoute;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decoratedFactory)($context);

        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['ShopUserToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['ShopUserCredentials'] = new \ArrayObject([
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
        ]);

        $tokenDocumentation = new PathItem(
            null,
            null,
            null,
            null,
            null,
            new Operation(
                'postCredentialsItem',
                ['ShopUserToken'],
                [
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
                'Get JWT token to login.',
                '',
                null,
                [],
                new RequestBody(
                    'Create new JWT Token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ShopUserCredentials',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        $openApi->getPaths()->addPath($this->apiRoute . '/shop/authentication-token', $tokenDocumentation);

        return $openApi;
    }
}
