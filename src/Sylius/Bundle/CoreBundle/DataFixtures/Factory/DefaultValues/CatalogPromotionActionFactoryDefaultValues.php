<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;

final class CatalogPromotionActionFactoryDefaultValues implements CatalogPromotionActionFactoryDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [],
        ];
    }
}
