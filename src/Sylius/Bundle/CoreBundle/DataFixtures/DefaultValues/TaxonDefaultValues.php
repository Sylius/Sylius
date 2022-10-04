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

final class TaxonDefaultValues implements TaxonDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'name' => $faker->unique()->words(3, true),
            'code' => null,
            'slug' => null,
            'description' => $faker->paragraph,
            'translations' => [],
            'children' => [],
            'parent' => null,
        ];
    }
}
