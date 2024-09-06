<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @template T of ShippingCategoryInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ShippingCategoryRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder;
}
