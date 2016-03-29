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

use Sylius\Bundle\AffiliateBundle\Doctrine\ORM\AffiliateGoalRepository as BaseAffiliateGoalRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\AffiliateGoalRepositoryInterface;


/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class AffiliateGoalRepository extends BaseAffiliateGoalRepository implements AffiliateGoalRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveByChannel(ChannelInterface $channel)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

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
