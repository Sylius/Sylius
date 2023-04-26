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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\DefaultValues;

use Faker\Generator;

final class CurrencyDefaultValues implements CurrencyDefaultValuesInterface
{
    public function getDefaultValues(Generator $faker): array
    {
        return [
            'code' => $faker->currencyCode(),
            'createdAt' => $faker->dateTime(),
        ];
    }
}
