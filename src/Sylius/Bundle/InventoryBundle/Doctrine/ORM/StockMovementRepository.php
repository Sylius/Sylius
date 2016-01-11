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
    public function createByLocationPaginator($locationId, $sorting = array())
    {
        $queryBuilder = $this->getQueryBuilder()
            ->leftJoin($this->getPropertyName('stockItem'), 'stockItem')
            ->addSelect('stockItem')
            ->andWhere('stockItem.stockLocation = :stockLocation')
            ->setParameter('stockLocation', $locationId);

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['createdAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
