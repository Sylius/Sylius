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
use Sylius\Component\Customer\Model\CustomerInterface;

final class CustomerDefaultValues implements CustomerDefaultValuesInterface
{
    public function getDefaultValues(Generator $faker): array
    {
        return [
            'createdAt' => $faker->dateTime(),
            'firstName' => $faker->firstName(),
            'lastName' => $faker->firstName(),
            'group' => null,
            'email' => $faker->email(),
            'gender' => CustomerInterface::UNKNOWN_GENDER,
            'phoneNumber' => $faker->phoneNumber(),
            'birthday' => $faker->dateTimeBetween('-80 years', '-18 years'),
            'subscribedToNewsletter' => $faker->boolean(),
        ];
    }
}
