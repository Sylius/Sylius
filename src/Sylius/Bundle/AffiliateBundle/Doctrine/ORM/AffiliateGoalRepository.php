<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Affiliate\Repository\AffiliateGoalRepositoryInterface;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class AffiliateGoalRepository extends EntityRepository implements AffiliateGoalRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActive()
    {
        $qb = $this->getCollectionQueryBuilder();

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
            ->leftJoin($this->getPropertyName('provisions'), 'p')
            ->addSelect('p');
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