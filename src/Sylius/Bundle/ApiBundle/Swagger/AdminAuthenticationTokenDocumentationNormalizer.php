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

namespace Sylius\Bundle\ApiBundle\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AdminAuthenticationTokenDocumentationNormalizer implements NormalizerInterface
{
    /** @var NormalizerInterface */
    private $decoratedNormalizer;

    public function __construct(NormalizerInterface $decoratedNormalizer)
    {
        $this->decoratedNormalizer = $decoratedNormalizer;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $docs = $this->decoratedNormalizer->normalize($object, $format, $context);

        $docs['components']['schemas']['AdminUserToken'] = [
            'type' => 'object',
            'properties' => [
                'access_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'expires_in' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'token_type' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'scope' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];
        $docs['components']['schemas']['AdminUserCredentials'] = [
            'type' => 'object',
            'properties' => [
                'client_id' => [
                    'type' => 'string',
                    'example' => '5sxomk2lm680w80w00s0kssc4so4cccg0ksokks4csc88skgo0',
                ],'client_secret' => [
                    'type' => 'string',
                    'example' => '3h14yv3xn8mcwow4480wkgcow0wcc4gsscccg4ckwkws8gc4os',
                ],
                'grant_type' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
                'username' => [
                    'type' => 'string',
                    'example' => 'api@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'sylius-api',
                ],
            ],
        ];

        $tokenDocumentation = [
            'paths' => [
                '/api/oauth/v2/token ' => [
                    'post' => [
                        'tags' => ['AdminUserToken'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => 'Get Oauth token to login.',
                        'requestBody' => [
                            'description' => 'Create new Oauth Token',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/AdminUserCredentials',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Get Oauth token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/AdminUserToken',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($tokenDocumentation, $docs);
    }
}
