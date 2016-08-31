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

use Sylius\Bundle\CartBundle\Doctrine\ORM\CartRepository as BaseCartRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CartRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CartRepository extends BaseCartRepository implements CartRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCartByIdAndChannel($id, ChannelInterface $channel)
    {
        return $this->createQueryBuilder('o')
            ->where('o.id = :id')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
