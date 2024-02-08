<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Generator;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException;

interface ProductVariantGeneratorInterface
{
    /**
     * @throws VariantWithNoOptionsValuesException
     * @throws \InvalidArgumentException
     */
    public function generate(ProductInterface $product): void;
}
