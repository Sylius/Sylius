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
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;

final class ZoneDefaultValues implements ZoneDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'code' => null,
            'name' => $faker->word(),
            'type' => ZoneInterface::TYPE_ZONE,
            'members' => [],
            'scope' => Scope::ALL,
        ];
    }
}
