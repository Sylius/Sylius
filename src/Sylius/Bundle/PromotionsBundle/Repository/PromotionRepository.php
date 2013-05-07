<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Promotion repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionRepository extends EntityRepository implements PromotionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        return $queryBuilder
            ->leftJoin($this->getAlias().'.rules', 'r')
            ->addSelect('r')
            ->leftJoin($this->getAlias().'.actions', 'a')
            ->addSelect('a')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getAlias().'.startsAt'),
                    $queryBuilder->expr()->lt($this->getAlias().'.startsAt', ':now')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getAlias().'.endsAt'),
                    $queryBuilder->expr()->gt($this->getAlias().'.endsAt', ':now')
                )
            )
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult()
        ;
    }
}
