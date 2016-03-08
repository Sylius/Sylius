<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionRepository extends EntityRepository implements PromotionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        $queryBuilder = $this
            ->createQueryBuilder('o')
            ->leftJoin($this->getPropertyName('rules'), 'r')
            ->addSelect('r')
            ->leftJoin($this->getPropertyName('actions'), 'a')
            ->addSelect('a')
            ->orderBy($this->getPropertyName('priority'), 'DESC')
        ;

        $this->filterByActive($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->createQueryBuilder('o')
            ->where('o.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param \Datetime|null $date
     *
     * @return QueryBuilder
     */
    protected function filterByActive(QueryBuilder $queryBuilder, \Datetime $date = null)
    {
        if (null === $date) {
            $date = new \Datetime();
        }

        return $queryBuilder
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getPropertyName('startsAt')),
                    $queryBuilder->expr()->lt($this->getPropertyName('startsAt'), ':date')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull($this->getPropertyName('endsAt')),
                    $queryBuilder->expr()->gt($this->getPropertyName('endsAt'), ':date')
                )
            )
            ->setParameter('date', $date)
        ;
    }
}
