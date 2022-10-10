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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class CustomerDefaultValues implements CustomerDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'email' => $faker->email(),
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'customer_group' => '',
            'gender' => CustomerInterface::UNKNOWN_GENDER,
            'phone_number' => $faker->phoneNumber(),
            'birthday' => $faker->dateTimeBetween('-80 years', '-18 years'),
        ];
    }
}
