<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

trait TransformProductAttributeTrait
{
    private ProductFactoryInterface $productFactory;

    private function transformProductOptionsAttribute(array $attributes): array
    {
        if (\is_string($attributes['product'])) {
            $attributes['product'] = $this->productFactory::findOrCreate(['code' => $attributes['product']]);
        }

        return $attributes;
    }
}
