<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Transformer;

final class CatalogPromotionScopeFactoryTransformer implements CatalogPromotionScopeFactoryTransformerInterface
{
    public function transform(array $attributes): array
    {
        return $attributes;
    }
}
