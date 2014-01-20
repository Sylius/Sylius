<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Repository;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * This interface should be implemented by repository of product.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * Find X recently added products.
     *
     * @param int $limit
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10);
}
