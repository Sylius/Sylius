<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

trait TransformProductAttributeTrait
{
    private ProductFactoryInterface $productFactory;

    private function transformProductAttribute(array $attributes, string $attributeKey = 'product'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $attributes[$attributeKey] = $this->productFactory::findOrCreate(['code' => $attributes[$attributeKey]]);
        }

        return $attributes;
    }
}
