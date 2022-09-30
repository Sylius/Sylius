<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionScopeFactoryInterface;

trait TransformCatalogPromotionScopesAttributeTrait
{
    private CatalogPromotionScopeFactoryInterface $promotionScopeFactory;

    private function transformScopesAttribute(array $attributes): array
    {
        $scopes = [];
        foreach ($attributes['scopes'] as $scope) {
            if (\is_array($scope)) {
                $scope = $this->promotionScopeFactory::new()->withAttributes($scope)->create();
            }

            $scopes[] = $scope;
        }

        $attributes['scopes'] = $scopes;

        return $attributes;
    }
}
