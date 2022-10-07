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

final class AddressDefaultValues implements AddressDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'phone_number' => $faker->boolean() ? $faker->phoneNumber() : null,
            'company' => $faker->boolean() ? $faker->company() : null,
            'street' => $faker->streetAddress(),
            'city' => $faker->city(),
            'postcode' => $faker->postcode(),
            'country_code' => null,
            'province_name' => null,
            'province_code' => null,
            'customer' => '',
        ];
    }
}
