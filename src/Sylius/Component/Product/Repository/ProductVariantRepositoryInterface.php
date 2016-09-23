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

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductVariantRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return ProductVariantInterface[]
     */
    public function findByName($name);

    /**
     * @param mixed $productId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithProduct($productId);
}
