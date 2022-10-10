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

final class ProductReviewDefaultValues implements ProductReviewDefaultValuesInterface
{
    public function getDefaults(Generator $faker): array
    {
        return [
            'title' => $faker->words(3, true),
            'rating' => $faker->numberBetween(1, 5),
            'comment' => $faker->sentences(3, true),
            'author' => null,
            'product' => null,
            'status' => null,
        ];
    }
}
