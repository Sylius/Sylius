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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\WishlistRepositoryInterface;

class WishlistRepository extends EntityRepository implements WishlistRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findEmailsByNotification($notifyOn)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->select('user.email')
            ->innerJoin('wi.product', 'product')
            ->innerJoin('wi.wishlist', 'wishlist')
            ->innerJoin('wishlist.user', 'user')
            ->where('wi.notifyOn = :notify')
            ->setParameter('notify', $notifyOn)
        ;

        return $queryBuilder
            ->getQuery()
            ->getArrayResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'wi';
    }
}
