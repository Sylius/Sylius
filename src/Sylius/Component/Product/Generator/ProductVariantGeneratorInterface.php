<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Generator;

use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantGeneratorInterface
{
    /**
     * @param ProductInterface $product
     *
     * @throws \InvalidArgumentException
     */
    public function generate(ProductInterface $product);
}
