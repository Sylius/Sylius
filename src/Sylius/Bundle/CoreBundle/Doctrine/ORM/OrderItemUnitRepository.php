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

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderItemUnitRepository as BaseOrderItemUnitRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Repository\OrderItemUnitRepositoryInterface;

/**
 * @template T of OrderItemUnitInterface
 * @extends BaseOrderItemUnitRepository<T>
 * @implements OrderItemUnitRepositoryInterface<T>
 */
class OrderItemUnitRepository extends BaseOrderItemUnitRepository implements OrderItemUnitRepositoryInterface
{
    public function findOneByCustomer($id, CustomerInterface $customer): ?OrderItemUnitInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.orderItem', 'orderItem')
            ->innerJoin('orderItem.order', 'ord')
            ->innerJoin('ord.customer', 'customer')
            ->andWhere('o.id = :id')
            ->andWhere('customer = :customer')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
