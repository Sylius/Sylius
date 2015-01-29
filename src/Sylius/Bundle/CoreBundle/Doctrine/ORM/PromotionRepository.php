<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;

/**
 * Promotion repository.
 *
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionRepository extends BasePromotionRepository
{
    /**
     * {@inheritdoc}
     */
    public function findActiveByChannel($channel)
    {
        $qb = $this
            ->getCollectionQueryBuilder()
            ->orderBy($this->getAlias().'.priority', 'DESC')
        ;

        $this->filterByActive($qb);

        $qb
            ->innerJoin($this->getAlias().'.channels', 'channel')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
