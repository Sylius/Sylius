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
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class AttributeTypeDocumentationModifier implements DocumentationModifierInterface
{
    public function __construct(
        private ServiceRegistryInterface $attributeTypeRegistry,
    ) {
    }

    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();
        if (null === $schemas) {
            return $docs;
        }

        $schemas = $this->updateAttributeTypesSchema($schemas);

        return $docs->withComponents($components->withSchemas($schemas));
    }

    /**
     * @param \ArrayObject<string, mixed> $schemas
     *
     * @return \ArrayObject<string, mixed>
     */
    private function updateAttributeTypesSchema(\ArrayObject $schemas): \ArrayObject
    {
        $attributeTypes = $this->getAttributeTypes();

        $schemasToBeUpdated = [
            'ProductAttribute.admin.product_attribute.read',
            'ProductAttribute.admin.product_attribute.create',
            'ProductAttribute.jsonld-admin.product_attribute.read',
            'ProductAttribute.jsonld-admin.product_attribute.create',
        ];

        foreach ($schemasToBeUpdated as $schemaToBeUpdated) {
            if (!isset($schemas[$schemaToBeUpdated])) {
                continue;
            }

            $schemas[$schemaToBeUpdated]['properties']['type'] = [
                'type' => 'string',
                'enum' => $attributeTypes,
            ];
        }

        return $schemas;
    }

    /** @return array<string> */
    private function getAttributeTypes(): array
    {
        $attributeTypes = [];

        /** @var AttributeTypeInterface $attributeType */
        foreach ($this->attributeTypeRegistry->all() as $attributeType) {
            $attributeTypes[] = $attributeType->getType();
        }

        return $attributeTypes;
    }
}
