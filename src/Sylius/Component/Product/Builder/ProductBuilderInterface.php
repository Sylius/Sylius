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

/**
 * Product builder interface.
 *
 * Goal of service implementing this interface is to ease the process of programatically creating products.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductBuilderInterface
{
    /**
     * Start creating the product with specified name.
     *
     * @param string $name
     *
     * @return ProductBuilderInterface
     */
    public function create($name);

    /**
     * Add attribute with name and value.
     *
     * @param string $name
     * @param mixed  $value
     * @param string $presentation
     *
     * @return ProductBuilderInterface
     */
    public function addAttribute($name, $value, $presentation = null);

    /**
     * Save the product
     *
     * @param Boolean $flush
     *
     * @return ProductInterface
     */
    public function save($flush = true);
}
