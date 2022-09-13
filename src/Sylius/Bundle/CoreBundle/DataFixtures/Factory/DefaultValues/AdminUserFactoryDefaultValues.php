<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\DefaultValues;

use Faker\Generator;

final class AdminUserFactoryDefaultValues implements AdminUserFactoryDefaultValuesInterface
{
    public function __construct(private string $localeCode)
    {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'email' => $faker->email(),
            'username' => $faker->firstName() . ' ' . $faker->lastName(),
            'enabled' => true,
            'password' => 'password123',
            'api' => false,
            'locale_code' => $this->localeCode,
            'first_name' => null,
            'last_name' => null,
            'avatar' => '',
        ];
    }
}
