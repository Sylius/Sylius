<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Util\FindOrCreateTaxCategoryTrait;

trait TransformTaxCategoryAttributeTrait
{
    use FindOrCreateTaxCategoryTrait;

    private function transformTaxCategoryAttribute(EventDispatcherInterface $eventDispatcher, array $attributes, string $attributeKey = 'tax_category'): array
    {
        if (\is_string($attributes[$attributeKey])) {
            $attributes[$attributeKey] = $this->findOrCreateTaxCategory($eventDispatcher, ['code' => $attributes[$attributeKey]]);
        }

        return $attributes;
    }
}
