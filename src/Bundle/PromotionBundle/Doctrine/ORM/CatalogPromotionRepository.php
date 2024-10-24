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

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

/**
 * @template T of CatalogPromotionInterface
 *
 * @implements CatalogPromotionRepositoryInterface<T>
 */
class CatalogPromotionRepository extends EntityRepository implements CatalogPromotionRepositoryInterface
{
    public function findByCriteria(iterable $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('o');

        /** @var CriteriaInterface $criterion */
        foreach ($criteria as $criterion) {
            $criterion->filterQueryBuilder($queryBuilder);
        }

        return $queryBuilder
            ->addSelect('scopes')
            ->addSelect('actions')
            ->leftJoin('o.scopes', 'scopes')
            ->leftJoin('o.actions', 'actions')
            ->orderBy('o.exclusive', 'desc')
            ->addOrderBy('o.priority', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }
}
