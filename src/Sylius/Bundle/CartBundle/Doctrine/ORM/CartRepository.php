<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Doctrine\ORM;

use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CartRepository extends OrderRepository implements CartRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCartById($id)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findExpiredCarts()
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'item')
            ->addSelect('item')
        ;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt('o.expiresAt', ':now'))
            ->andWhere($queryBuilder->expr()->eq('o.state', ':state'))
            ->setParameter('now', new \DateTime())
            ->setParameter('state', OrderInterface::STATE_CART)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
