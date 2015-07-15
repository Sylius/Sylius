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
        $qb = $this
            ->getCollectionQueryBuilder()
            ->orderBy($this->getPropertyName('priority'), 'DESC')
        ;

        $this->filterByActive($qb);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin($this->getPropertyName('rules'), 'r')
            ->addSelect('r')
            ->leftJoin($this->getPropertyName('actions'), 'a')
            ->addSelect('a');
    }

    protected function filterByActive(QueryBuilder $qb, \Datetime $date = null)
    {
        if (null === $date) {
            $date = new \Datetime();
        }

        return $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->isNull($this->getPropertyName('startsAt')),
                    $qb->expr()->lt($this->getPropertyName('startsAt'), ':date')
                )
            )
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull($this->getPropertyName('endsAt')),
                    $qb->expr()->gt($this->getPropertyName('endsAt'), ':date')
                )
            )
            ->setParameter('date', $date)
        ;
    }
}
