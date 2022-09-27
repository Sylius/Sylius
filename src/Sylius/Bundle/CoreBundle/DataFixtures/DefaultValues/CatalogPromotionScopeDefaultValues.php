<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;

final class CatalogPromotionScopeDefaultValues implements CatalogPromotionScopeDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'type' => InForProductScopeVariantChecker::TYPE,
            'configuration' => [],
        ];
    }
}
