<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItemRepository extends EntityRepository implements StockItemRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function countOnHandByStockable(StockableInterface $stockable)
    {
        return $this->getQueryBuilder()
            ->select('SUM('.$this->getPropertyName('onHand').')')
            ->where($this->getPropertyName('stockable').' = :stockable')
            ->setParameter('stockable', $stockable)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function countOnHoldByStockable(StockableInterface $stockable)
    {
        return $this->getQueryBuilder()
            ->select('SUM('.$this->getPropertyName('onHold').')')
            ->where($this->getPropertyName('stockable').' = :stockable')
            ->setParameter('stockable', $stockable)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByStockableAndLocation(StockableInterface $stockable, StockLocationInterface $location)
    {
        return $this->findOneBy(array(
            'stockable' => $stockable,
            'location' => $location,
        ));
    }

    /**
     * @param int $locationId
     */
    public function createByLocationPaginator($locationId)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->andWhere($this->getPropertyName('location').' = :location')
            ->setParameter('location', $locationId)
        ;

        return $this->getPaginator($queryBuilder);
    }
}
