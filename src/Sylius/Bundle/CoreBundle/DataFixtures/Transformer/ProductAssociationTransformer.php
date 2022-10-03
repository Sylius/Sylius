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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

final class ProductAssociationTransformer implements ProductAssociationTransformerInterface
{
    use TransformProductAttributeTrait;

    public function __construct(
        private ProductAssociationTypeFactoryInterface $associationTypeFactory,
        private ProductFactoryInterface $productFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformAssociationTypeAttribute($attributes);
        $attributes = $this->transformAssociatedProductsAttribute($attributes);

        return $this->transformProductAttribute($attributes, 'owner');
    }

    private function transformAssociationTypeAttribute(array $attributes): array
    {
        if (\is_string($attributes['type'])) {
            $attributes['type'] = $this->associationTypeFactory::findOrCreate(['code' => $attributes['type']]);
        }

        return $attributes;
    }

    private function transformAssociatedProductsAttribute(array $attributes): array
    {
        $products = [];
        foreach ($attributes['associated_products'] as $product) {
            if (\is_string($product)) {
                $product = $this->productFactory::findOrCreate(['code' => $product]);
            }

            $products[] = $product;
        }

        $attributes['associated_products'] = $products;

        return $attributes;
    }
}
