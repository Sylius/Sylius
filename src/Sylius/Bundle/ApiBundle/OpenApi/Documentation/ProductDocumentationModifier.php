<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;

/** @experimental */
final class ProductDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['Product.jsonld-shop.product.read']['properties']['defaultVariant'] = [
            'type' => 'string',
            'format' => 'iri-reference',
            'nullable' => true,
            'readOnly' => true,
        ];

        return $docs->withComponents(
            $components->withSchemas($schemas)
        );
    }
}
