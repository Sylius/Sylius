<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ShippingBundle\Doctrine\ORM\ShippingMethodRepository as BaseShippingMethodRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;

/**
 * @template T of ShippingMethodInterface
 *
 * @extends BaseShippingMethodRepository<T>
 *
 * @implements ShippingMethodRepositoryInterface<T>
 */
class ShippingMethodRepository extends BaseShippingMethodRepository implements ShippingMethodRepositoryInterface
{
    public function createListQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }

    public function findEnabledForChannel(ChannelInterface $channel): array
    {
        return $this->createEnabledForChannelQueryBuilder($channel)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findEnabledForZonesAndChannel(array $zones, ChannelInterface $channel): array
    {
        return $this->createEnabledForChannelQueryBuilder($channel)
            ->andWhere('o.zone IN (:zones)')
            ->setParameter('zones', $zones)
            ->getQuery()
            ->getResult()
        ;
    }

    protected function createEnabledForChannelQueryBuilder(ChannelInterface $channel): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.enabled = :enabled')
            ->andWhere('o.archivedAt IS NULL')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->setParameter('enabled', true)
            ->addOrderBy('o.position', 'ASC')
        ;
    }
}
