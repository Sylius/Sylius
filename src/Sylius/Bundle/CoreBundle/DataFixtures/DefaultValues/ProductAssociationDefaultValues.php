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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductAssociationTypeFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ProductFactoryInterface;

final class ProductAssociationDefaultValues implements ProductAssociationDefaultValuesInterface
{
    public function __construct(
        private ProductAssociationTypeFactoryInterface $associationTypeFactory,
        private ProductFactoryInterface $productFactory,
    ) {
    }

    public function getDefaults(Generator $faker): array
    {
        return [
            'type' => $this->associationTypeFactory::randomOrCreate(),
            'owner' => $this->productFactory::randomOrCreate(),
            'associated_products' => [],
        ];
    }
}
