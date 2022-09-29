<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;

trait TransformTaxCategoryAttributeTrait
{
    private TaxCategoryFactoryInterface $taxCategoryFactory;

    private function transformTaxCategoryAttribute(array $attributes): array
    {
        if (\is_string($attributes['tax_category'])) {
            $attributes['tax_category'] = $this->taxCategoryFactory::findOrCreate(['code' => $attributes['tax_category']]);
        }

        return $attributes;
    }
}
