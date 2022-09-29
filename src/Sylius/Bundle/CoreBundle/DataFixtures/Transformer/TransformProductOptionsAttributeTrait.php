<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductOptionFactoryInterface;

trait TransformProductOptionsAttributeTrait
{
    private ProductOptionFactoryInterface $productOptionFactory;

    private function transformProductOptionsAttribute(array $attributes): array
    {
        $productOptions = [];
        foreach ($attributes['product_options'] as $productOption) {
            if (\is_string($productOption)) {
                $productOption = $this->productOptionFactory::findOrCreate(['code' => $productOption]);
            }
            $productOptions[] = $productOption;
        }
        $attributes['product_options'] = $productOptions;

        return $attributes;
    }
}
