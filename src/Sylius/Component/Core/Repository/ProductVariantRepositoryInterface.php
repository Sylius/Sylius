<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface as BaseProductVariantRepositoryInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ProductVariantRepositoryInterface extends BaseProductVariantRepositoryInterface
{
    /**
     * @param string $locale
     *
     * @return QueryBuilder
     */
    public function createInventoryListQueryBuilder($locale);
}
