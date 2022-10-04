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

final class ProductAttributeDefaultValues implements ProductAttributeDefaultValuesInterface
{
    public function __construct(
        private array $attributeTypes,
    ) {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'code' => null,
            'name' => $faker->words(3, true),
            'type' => $faker->randomElement(array_keys($this->attributeTypes)),
            'translatable' => true,
            'configuration' => [],
        ];
    }
}
