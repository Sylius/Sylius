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
use Sylius\Component\Inventory\Repository\StockMovementRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementRepository extends EntityRepository implements StockMovementRepositoryInterface
{
    /**
     * @param int $locationId
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function createByLocationPaginator($locationId)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->leftJoin($this->getPropertyName('stockItem'), 'stockItem')
            ->addSelect('stockItem')
            ->andWhere('stockItem.stockLocation = :stockLocation')
            ->setParameter('stockLocation', $locationId);

        return $this->getPaginator($queryBuilder);
    }
}
