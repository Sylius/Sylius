<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues;

use Faker\Generator;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;

final class PromotionRuleFactoryDefaultValues implements PromotionRuleFactoryDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'type' => CartQuantityRuleChecker::TYPE,
            'configuration' => ['count' => $faker->randomNumber(1)],
        ];
    }
}
