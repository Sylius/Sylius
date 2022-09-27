<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

final class CatalogPromotionScopeTransformer implements CatalogPromotionScopeTransformerInterface
{
    public function transform(array $attributes): array
    {
        return $attributes;
    }
}
