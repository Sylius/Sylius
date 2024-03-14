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

        if (!isset($schemas['ProductVariant.jsonld-shop.product_variant.read'])) {
            return $docs;
        }

        $schemas['ProductVariant.jsonld-shop.product_variant.read']['properties']['price'] = [
            'type' => 'integer',
            'readOnly' => true,
            'default' => 0,
        ];

        $schemas['ProductVariant.jsonld-shop.product_variant.read']['properties']['inStock'] = [
            'type' => 'boolean',
            'readOnly' => true,
        ];

        $schemas['ProductVariant.jsonld-shop.product_variant.read']['properties']['originalPrice'] = [
            'type' => 'integer',
            'readOnly' => true,
            'default' => 0,
        ];

        return $docs->withComponents(
            $components->withSchemas($schemas),
        );
    }
}
