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
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Repository\StockLocationRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationRepository extends EntityRepository implements StockLocationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function countBackorderableByStockable(StockableInterface $stockable)
    {
        return $this->getQueryBuilder()
            ->leftJoin($this->getPropertyName('stockItems'), 'i')
            ->select('COUNT('.$this->getPropertyName('id').')')
            ->where($this->getPropertyName('stockable').' = :stockable')
            ->setParameter('stockable', $stockable)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllEnabled()
    {
        return $this->findBy(array('enabled' => true));
    }
}
