<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Product\Repository;

use Sylius\Product\Model\ProductInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return ProductInterface|null
     */
    public function findOneByName($name);

    /**
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findOneBySlug($slug);
}
