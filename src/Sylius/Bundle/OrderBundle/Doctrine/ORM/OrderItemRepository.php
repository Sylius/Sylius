<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;

/**
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
class OrderItemRepository extends EntityRepository implements OrderItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByIdAndCartId($id, $cartId)
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
}
