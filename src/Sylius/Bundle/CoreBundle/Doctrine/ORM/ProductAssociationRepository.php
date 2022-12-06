<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductAssociationRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;

class ProductAssociationRepository extends EntityRepository implements ProductAssociationRepositoryInterface
{
    public function findAssociatedProductsWithinChannel($associationId, $productId, ChannelInterface $channel): ProductAssociationInterface
    {
        return $this->createQueryBuilder('o')
            ->addSelect('associatedProduct')
            ->innerJoin('o.owner', 'product')
            ->innerJoin('o.associatedProducts', 'associatedProduct')
            ->innerJoin('associatedProduct.channels', 'channel')
            ->andWhere('o.id = :associationId')
            ->andWhere('product.id = :productId')
            ->andWhere('associatedProduct.enabled = 1')
            ->andWhere('channel = :channel')
            ->setParameter('associationId', $associationId)
            ->setParameter('productId', $productId)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
