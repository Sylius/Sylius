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

namespace Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\TaxCategoryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ZoneFactoryInterface;

final class TaxRateDefaultValues implements TaxRateDefaultValuesInterface
{
    public function __construct(
        private ZoneFactoryInterface $zoneFactory,
        private TaxCategoryFactoryInterface $taxCategoryFactory,
    ) {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'code' => null,
            'name' => $faker->words(3, true),
            'amount' => $faker->randomFloat(2, 0, 0.4),
            'included_in_price' => $faker->boolean(),
            'calculator' => 'default',
            'zone' => $this->zoneFactory->randomOrCreate(),
            'category' => $this->taxCategoryFactory->randomOrCreate(),
        ];
    }
}
