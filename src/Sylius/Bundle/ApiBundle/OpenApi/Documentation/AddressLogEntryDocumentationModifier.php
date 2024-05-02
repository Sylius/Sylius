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

use ApiPlatform\OpenApi\OpenApi;

final class AddressLogEntryDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        if (isset($schemas['Address-admin.address.log_entry.read'])) {
            $schemas['Address-admin.address.log_entry.read'] = [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'action' => [
                            'readOnly' => true,
                            'type' => 'string',
                        ],
                        'version' => [
                            'readOnly' => true,
                            'type' => 'integer',
                        ],
                        'data' => [
                            'readOnly' => true,
                            'type' => 'object',
                        ],
                        'logged_at' => [
                            'readOnly' => true,
                            'type' => 'string',
                            'format' => 'date-time',
                        ],
                    ],
                ],
            ];
        }

        if (isset($schemas['Address.jsonld-admin.address.log_entry.read'])) {
            $schemas['Address.jsonld-admin.address.log_entry.read'] = [
                'type' => 'object',
                'properties' => [
                    'hydra:member' => [
                        'readOnly' => true,
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                '@type' => [
                                    'readOnly' => true,
                                    'type' => 'string',
                                ],
                                'action' => [
                                    'readOnly' => true,
                                    'type' => 'string',
                                ],
                                'version' => [
                                    'readOnly' => true,
                                    'type' => 'integer',
                                ],
                                'data' => [
                                    'readOnly' => true,
                                    'type' => 'object',
                                ],
                                'logged_at' => [
                                    'readOnly' => true,
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                    ],
                    'hydra:totalItems' => [
                        'readOnly' => true,
                        'type' => 'integer',
                    ],
                ],
            ];
        }

        $components = $components->withSchemas($schemas);

        return $docs->withComponents($components);
    }
}
