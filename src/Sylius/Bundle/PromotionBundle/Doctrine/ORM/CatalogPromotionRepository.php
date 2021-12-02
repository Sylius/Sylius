<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

class CatalogPromotionRepository extends EntityRepository implements CatalogPromotionRepositoryInterface
{
    public function findByCriteria(iterable $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('catalogPromotion');

        /** @var CriteriaInterface $criterion */
        foreach ($criteria as $criterion) {
            $criterion->filterQueryBuilder($queryBuilder);
        }

        return $queryBuilder
            ->orderBy('catalogPromotion.exclusive', 'desc')
            ->addOrderBy('catalogPromotion.priority', 'desc')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCodes(array $codes): ?array
    {
        return $this->createQueryBuilder('catalogPromotion')
            ->andWhere('catalogPromotion.code IN (:codes)')
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult()
        ;
    }
}
