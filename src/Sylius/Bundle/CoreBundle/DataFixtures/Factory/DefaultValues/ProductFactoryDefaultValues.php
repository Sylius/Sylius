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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactoryInterface;

final class ProductFactoryDefaultValues implements ProductFactoryDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'name' => $faker->words(3, true),
            'code' => null,
            'enabled' => true,
            'tracked' => false,
            'slug' => null,
            'short_description' => $faker->paragraph(),
            'description' => $faker->paragraphs(3, true),
        ];
    }
}
