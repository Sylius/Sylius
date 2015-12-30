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
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;

/**
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveByChannel(ChannelInterface $channel)
    {
        $queryBuilder = $this
            ->createQueryBuilder('o')
            ->orderBy($this->getPropertyName('priority'), 'DESC')
        ;

        $this->filterByActive($queryBuilder);

        $queryBuilder
            ->innerJoin($this->getPropertyName('channels'), 'channel')
            ->andWhere($queryBuilder->expr()->eq('channel', ':channel'))
            ->setParameter('channel', $channel)
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }
}
