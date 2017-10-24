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

namespace Sylius\Component\Product\Generator;

use Sylius\Component\Product\Model\ProductInterface;

interface ProductVariantGeneratorInterface
{
    /**
     * @param ProductInterface $product
     *
     * @throws \InvalidArgumentException
     */
    public function generate(ProductInterface $product): void;
}
