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

/** @experimental */
final class ShippingMethodDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        $components = $docs->getComponents();
        $schemas = $components->getSchemas();

        $schemas['ShippingMethod.jsonld-shop.shipping_method.read']['properties']['price'] = [
            'type' => 'integer',
            'readOnly' => true,
            'default' => 0,
        ];

        return $docs->withComponents(
            $components->withSchemas($schemas),
        );
    }
}
