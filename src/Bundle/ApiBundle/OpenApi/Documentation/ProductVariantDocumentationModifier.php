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

final class ProductVariantDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemasToBeUpdated = [
            'ProductVariant-sylius.shop.product_variant.index',
            'ProductVariant-sylius.shop.product_variant.show',
            'ProductVariant.jsonld-sylius.shop.product_variant.index',
            'ProductVariant.jsonld-sylius.shop.product_variant.show',
        ];

        foreach ($schemasToBeUpdated as $schemaToBeUpdated) {
            if (!isset($schemas[$schemaToBeUpdated])) {
                continue;
            }

            $schemas[$schemaToBeUpdated]['properties']['inStock'] = [
                'type' => 'boolean',
                'readOnly' => true,
            ];

            $schemas[$schemaToBeUpdated]['properties']['price'] = [
                'type' => 'integer',
                'readOnly' => true,
                'default' => 0,
            ];

            $schemas[$schemaToBeUpdated]['properties']['originalPrice'] = [
                'type' => 'integer',
                'readOnly' => true,
                'default' => 0,
            ];

            $schemas[$schemaToBeUpdated]['properties']['lowestPriceBeforeDiscount'] = [
                'type' => 'integer',
                'readOnly' => true,
                'default' => null,
            ];

            $schemas[$schemaToBeUpdated]['properties']['appliedPromotions'] = [
                'type' => 'array',
                'readOnly' => true,
                'items' => [
                    'type' => 'string',
                    'format' => 'iri-reference',
                ],
            ];
        }

        return $docs->withComponents($components->withSchemas($schemas));
    }
}
