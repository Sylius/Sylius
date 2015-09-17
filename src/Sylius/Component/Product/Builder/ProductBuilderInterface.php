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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductBuilderInterface
{
    /**
     * @param string $name
     *
     * @return ProductBuilderInterface
     */
    public function create($name);

    /**
     * @param string $name
     * @param mixed  $value
     * @param string $presentation
     *
     * @return ProductBuilderInterface
     */
    public function addAttribute($name, $value, $presentation = null);

    /**
     * @param bool $flush
     *
     * @return ProductInterface
     */
    public function save($flush = true);
}
