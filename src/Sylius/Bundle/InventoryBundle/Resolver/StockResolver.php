<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Resolver;

use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Default stock resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
class StockResolver implements StockResolverInterface
{
    /**
     * Inventory unit manager.
     *
     * @var InventoryUnitManagerInterface
     */
    protected $inventoryUnitManager;

    /**
     * Full inventory tracking enabled?
     *
     * @var Boolean
     */
    protected $tracking;

    /**
     * Constructor.
     *
     * @param InventoryUnitManagerInterface $inventoryUnitManager
     * @param Boolean                       $tracking
     */
    public function __construct(InventoryUnitManagerInterface $inventoryUnitManager, $tracking = true)
    {
        $this->inventoryUnitManager = $inventoryUnitManager;
        $this->tracking = (Boolean) $tracking;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(StockableInterface $stockable, $strict = true)
    {
        if ($strict && $this->tracking) {

            return 0 !== $this->inventoryUnitManager->getAvailableUnits($stockable);
        }

        return $stockable->isAvailable();
    }

    /**
     * {@inheritdoc}
     */
    public function getStock(StockableInterface $stockable)
    {
        return $this->inventoryUnitManager->countInventoryUnitsBy($stockable, array(
            'state' => InventoryUnitInterface::STATE_AVAILABLE
        ));
    }
}
