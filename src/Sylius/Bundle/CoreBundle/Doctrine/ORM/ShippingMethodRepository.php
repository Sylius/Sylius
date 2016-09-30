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
        return $this->createQueryBuilder('o')
            ->where('o.enabled = true')
            ->andWhere('o IN (:channelShippingMethods)')
            ->andWhere('o.zone IN (:zones)')
            ->setParameter('channelShippingMethods', $channel->getShippingMethods()->toArray())
            ->setParameter('zones', $zones)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findEnabledForChannel(ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = true')
            ->andWhere('o IN (:channelShippingMethods)')
            ->setParameter('channelShippingMethods', $channel->getShippingMethods()->toArray())
            ->getQuery()
            ->getResult()
        ;
    }
}
