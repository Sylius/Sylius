<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Builder;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;

/**
 * Prototype builder interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PrototypeBuilderInterface
{
    /**
     * Build the prototype of product.
     *
     * @param PrototypeInterface $prototype
     * @param ProductInterface   $product
     */
    public function build(PrototypeInterface $prototype, ProductInterface $product);
}
