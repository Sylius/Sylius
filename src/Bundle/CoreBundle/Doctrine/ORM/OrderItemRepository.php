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

use Doctrine\DBAL\Types\Types;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemRepository as BaseOrderItemRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;

/**
 * @template T of OrderItemInterface
 *
 * @extends BaseOrderItemRepository<T>
 *
 * @implements OrderItemRepositoryInterface<T>
 */
class OrderItemRepository extends BaseOrderItemRepository implements OrderItemRepositoryInterface
{
    public function findOneByIdAndCustomer($id, CustomerInterface $customer): ?OrderItemInterface
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

    public function findOneByIdAndOrderTokenValue(int $id, string $tokenValue): ?OrderItemInterface
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
        ;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->andWhere($queryBuilder->expr()->eq('ord.tokenValue', ':tokenValue'))
            ->setParameter('id', $id, Types::BIGINT)
            ->setParameter('tokenValue', $tokenValue, Types::STRING)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
