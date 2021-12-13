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
final class ProductVariantDocumentationFactory implements OpenApiFactoryInterface
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

        $schemas['ProductVariant.jsonld-shop.product_variant.read']['properties']['price'] = new \ArrayObject([
            'type' => 'int',
            'readOnly' => true,
            'default' => 0,
        ]);

        $schemas['ProductVariant.jsonld-shop.product_variant.read']['properties']['inStock'] = new \ArrayObject([
            'type' => 'boolean',
            'readOnly' => true,
        ]);

        return $openApi;
    }
}
