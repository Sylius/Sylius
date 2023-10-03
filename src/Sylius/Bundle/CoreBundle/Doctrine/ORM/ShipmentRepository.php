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
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

/**
 * @template T of ShipmentInterface
 *
 * @implements ShipmentRepositoryInterface<T>
 */
class ShipmentRepository extends EntityRepository implements ShipmentRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state != :state')
            ->setParameter('state', ShipmentInterface::STATE_CART)
        ;
    }

    public function findOneByOrderId($shipmentId, $orderId): ?ShipmentInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :shipmentId')
            ->andWhere('o.order = :orderId')
            ->setParameter('shipmentId', $shipmentId)
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByOrderTokenAndChannel($shipmentId, string $tokenValue, ChannelInterface $channel): ?ShipmentInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'orders')
            ->andWhere('o.id = :shipmentId')
            ->andWhere('orders.tokenValue = :tokenValue')
            ->andWhere('orders.channel = :channel')
            ->setParameter('shipmentId', $shipmentId)
            ->setParameter('tokenValue', $tokenValue)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCustomer($id, CustomerInterface $customer): ?ShipmentInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->innerJoin('ord.customer', 'customer')
            ->andWhere('o.id = :id')
            ->andWhere('customer = :customer')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('localeCode', $locale)
            ->getQuery()
            ->getResult()
        ;
    }
}
