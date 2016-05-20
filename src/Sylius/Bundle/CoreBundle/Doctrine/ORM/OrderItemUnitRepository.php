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
use Sylius\Component\Inventory\Repository\InventoryUnitRepositoryInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
class OrderItemUnitRepository extends EntityRepository implements InventoryUnitRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByStockableAndInventoryState(StockableInterface $stockable, $state, $limit = null)
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.orderItem', 'item')
            ->where('item.variant = :variant')
            ->setParameter('variant', $stockable)
            ->andWhere('o.inventoryState = :state')
            ->setParameter('state', $state)
            ->orderBy('o.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
