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
use Sylius\Component\Inventory\Repository\InventoryUnitRepositoryInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
class InventoryUnitRepository extends EntityRepository implements InventoryUnitRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByStockableAndInventoryState(StockableInterface $stockable, $state, $limit = null)
    {
        return $this->findBy(
            [
                'stockable' => $stockable,
                'inventoryState' => $state,
            ],
            [
                'createdAt' => 'ASC',
            ],
            $limit
        );
    }
}
