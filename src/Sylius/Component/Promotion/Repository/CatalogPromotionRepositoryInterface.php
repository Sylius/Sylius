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

namespace Sylius\Component\Promotion\Repository;

use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of CatalogPromotionInterface
 *
 * @extends RepositoryInterface<T>
 */
interface CatalogPromotionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param iterable|CriteriaInterface[] $criteria
     */
    public function findByCriteria(iterable $criteria): array;
}
