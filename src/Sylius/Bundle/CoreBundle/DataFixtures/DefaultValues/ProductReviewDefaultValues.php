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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CountryFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShopUserFactoryInterface;

final class ProductReviewDefaultValues implements ProductReviewDefaultValuesInterface
{
    public function __construct(
        private ShopUserFactoryInterface $shopUserFactory,
        private ProductFactoryInterface $productFactory,
    ) {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'title' => $faker->words(3, true),
            'rating' => $faker->numberBetween(1, 5),
            'comment' => $faker->sentences(3, true),
            'author' => $this->shopUserFactory::randomOrCreate()->getCustomer(),
            'product' => $this->productFactory::randomOrCreate(),
            'status' => null,
        ];
    }
}
