<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Builder;

use Sylius\Bundle\ProductBundle\Model\ProductInterface;

/**
 * Product builder interface.
 *
 * Goal of service implementing this interface is to ease the process of programatically creating products.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
     * Add property with name and value.
     *
     * @param string $name
     * @param mixed  $value
     * @param string $presentation
     *
     * @return ProductBuilderInterface
     */
    public function addProperty($name, $value, $presentation = null);

    /**
     * Save the product
     *
     * @param Boolean $flush
     *
     * @return ProductInterface
     */
    public function save($flush = true);
}
