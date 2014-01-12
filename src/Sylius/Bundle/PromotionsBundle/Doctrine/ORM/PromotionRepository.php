<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface;
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
        $qb = $this->getCollectionQueryBuilder();

        $this->filterByActive($qb);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAppliedOnSubject(PromotionSubjectInterface $subject)
    {
        $qb = $this->getCollectionQueryBuilder();

        $this->filterBySubject($qb, $subject);

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->leftJoin($this->getAlias().'.rules', 'r')
            ->addSelect('r')
            ->leftJoin($this->getAlias().'.actions', 'a')
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
                    $qb->expr()->isNull($this->getAlias().'.startsAt'),
                    $qb->expr()->lt($this->getAlias().'.startsAt', ':date')
                )
            )
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->isNull($this->getAlias().'.endsAt'),
                    $qb->expr()->gt($this->getAlias().'.endsAt', ':date')
                )
            )
            ->setParameter('date', $date);
    }

    protected function filterBySubject(QueryBuilder $qb, PromotionSubjectInterface $subject)
    {
        return $qb
            ->join($this->getAlias().'.subjects', 's', 'WITH', 's = :subject')
            ->setParameter('subject', $subject);
    }
}
