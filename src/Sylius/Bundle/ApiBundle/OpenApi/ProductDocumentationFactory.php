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

namespace Sylius\Bundle\ApiBundle\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;

/** @experimental */
final class ProductDocumentationFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decoratedFactory;

    public function __construct(OpenApiFactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decoratedFactory)($context);

        $schemas = $openApi->getComponents()->getSchemas();

        $defaultVariantSchema = new \ArrayObject([
            'type' => 'string',
            'format' => 'iri-reference',
            'nullable' => true,
            'readOnly' => true,
        ]);

        $schemas['Product.jsonld-admin.product.read']['properties']['defaultVariant'] = $defaultVariantSchema;
        $schemas['Product.jsonld-shop.product.read']['properties']['defaultVariant'] = $defaultVariantSchema;

        return $openApi;
    }
}
