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

use Datetime;
use Sylius\Bundle\CartBundle\Repository\CartRepositoryInterface;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;

/**
 * Default cart entity repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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
            ->andWhere($queryBuilder->expr()->in($this->getAlias().'.state', ':states'))
            ->setParameter('now', new Datetime())
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CART_LOCKED, OrderInterface::STATE_PENDING])
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
