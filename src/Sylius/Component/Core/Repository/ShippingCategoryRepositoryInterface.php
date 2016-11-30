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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */ 
interface ShippingCategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();
}
