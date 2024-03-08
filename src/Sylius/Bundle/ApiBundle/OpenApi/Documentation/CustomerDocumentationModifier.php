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

final class CustomerDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $docs = $this->updateVerifiedPropertyType($docs);
        $docs = $this->updateCustomerStatisticsExampleResponse($docs);

        return $docs;
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
}
