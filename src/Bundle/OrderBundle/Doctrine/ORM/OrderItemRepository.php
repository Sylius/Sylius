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

namespace Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;

/**
 * @template T of OrderItemInterface
 *
 * @implements OrderItemRepositoryInterface<T>
 */
class OrderItemRepository extends EntityRepository implements OrderItemRepositoryInterface
{
    public function findOneByIdAndCartId($id, $cartId): ?OrderItemInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'cart')
            ->andWhere('cart.state = :state')
            ->andWhere('o.id = :id')
            ->andWhere('cart.id = :cartId')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('id', $id)
            ->setParameter('cartId', $cartId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByIdAndCartTokenValue($id, $tokenValue): ?OrderItemInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'cart')
            ->andWhere('cart.state = :state')
            ->andWhere('o.id = :id')
            ->andWhere('cart.tokenValue = :tokenValue')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('id', $id)
            ->setParameter('tokenValue', $tokenValue)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
