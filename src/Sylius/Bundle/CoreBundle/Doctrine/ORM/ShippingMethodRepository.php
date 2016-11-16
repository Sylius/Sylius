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

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ShippingBundle\Doctrine\ORM\ShippingMethodRepository as BaseShippingMethodRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ShippingMethodRepository extends BaseShippingMethodRepository implements ShippingMethodRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder($locale)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledForZonesAndChannel(array $zones, ChannelInterface $channel)
    {
        return $this
            ->getEnabledForChannelQueryBuilder($channel)
            ->andWhere('o.zone IN (:zones)')
            ->setParameter('zones', $zones)
            ->orderBy('o.position', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledForChannel(ChannelInterface $channel)
    {
        return $this
            ->getEnabledForChannelQueryBuilder($channel)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return QueryBuilder
     */
    private function getEnabledForChannelQueryBuilder(ChannelInterface $channel)
    {
        $queryBuilder = $this
            ->createQueryBuilder('o')
            ->where('o.enabled = true')
        ;

        $queryBuilder
            ->innerJoin($this->getPropertyName('channels'), 'channel')
            ->andWhere($queryBuilder->expr()->eq('channel', ':channel'))
            ->setParameter('channel', $channel)
        ;

        return $queryBuilder;
    }
}
