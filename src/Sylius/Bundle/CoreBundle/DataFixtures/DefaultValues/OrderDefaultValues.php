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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerFactoryInterface;

final class OrderDefaultValues implements OrderDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'channel' => null,
            'customer' => null,
            'country' => null,
            'complete_date' => $faker->dateTimeBetween('-1 years', 'now'),
            'fulfilled' => false,
        ];
    }
}
