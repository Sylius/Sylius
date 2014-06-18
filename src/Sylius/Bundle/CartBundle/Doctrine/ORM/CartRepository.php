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

use Sylius\Bundle\CartBundle\Repository\CartRepositoryInterface;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * Default cart entity repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CartRepository extends OrderRepository implements CartRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findExpiredCarts()
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt($this->getAlias().'.expiresAt', ':now'))
            ->andWhere($queryBuilder->expr()->eq($this->getAlias().'.state', ':state'))
            ->setParameter('now', new \DateTime())
            ->setParameter('state', OrderInterface::STATE_CART)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
