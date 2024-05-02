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

final class ProductDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        if (!isset($schemas['Product.jsonld-shop.product.read'])) {
            return $docs;
        }

        $schemas['Product.jsonld-shop.product.read']['properties']['defaultVariant'] = [
            'type' => 'string',
            'format' => 'iri-reference',
            'nullable' => true,
            'readOnly' => true,
        ];

        return $docs->withComponents(
            $components->withSchemas($schemas),
        );
    }
}
