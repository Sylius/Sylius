<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues;

use Faker\Generator;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;

final class PromotionActionFactoryDefaultValues implements PromotionActionFactoryDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'type' => PercentageDiscountPromotionActionCommand::TYPE,
            'configuration' => ['percentage' => $faker->randomNumber(2)],
        ];
    }
}
