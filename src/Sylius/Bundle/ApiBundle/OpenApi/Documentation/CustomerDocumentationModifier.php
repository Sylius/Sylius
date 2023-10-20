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
final class CustomerDocumentationModifier implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
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

        return $docs->withComponents(
            $components->withSchemas($schemas),
        );
    }
}
