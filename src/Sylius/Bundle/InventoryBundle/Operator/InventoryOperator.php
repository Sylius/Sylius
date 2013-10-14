<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Operator;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Default inventory operator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryOperator implements InventoryOperatorInterface
{
    /**
     * Backorders handler.
     *
     * @var BackordersHandlerInterface
     */
    protected $backordersHandler;

    /**
     * Availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    protected $availabilityChecker;

    /**
     * Constructor.
     *
     * @param BackordersHandlerInterface   $backordersHandler
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(BackordersHandlerInterface $backordersHandler, AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->backordersHandler = $backordersHandler;
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function increase(StockableInterface $stockable, $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1.');
        }

        $stockable->setOnHand($stockable->getOnHand() + $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function decrease($inventoryUnits)
    {
        if (!is_array($inventoryUnits) && !$inventoryUnits instanceof Collection) {
            throw new \InvalidArgumentException('Inventory units value must be array or instance of "Doctrine\Common\Collections\Collection".');
        }

        $quantity = count($inventoryUnits);

        if ($quantity < 1) {
            throw new \InvalidArgumentException('Quantity of units must be greater than 1.');
        }

        if ($inventoryUnits instanceof Collection) {
            $stockable = $inventoryUnits->first()->getStockable();
        } else {
            $stockable = $inventoryUnits[0]->getStockable();
        }

        if (!$this->availabilityChecker->isStockSufficient($stockable, $quantity)) {
            throw new InsufficientStockException($stockable, $quantity);
        }

        $this->backordersHandler->processBackorders($inventoryUnits);

        $onHand = $stockable->getOnHand();

        foreach ($inventoryUnits as $inventoryUnit) {
            if (InventoryUnitInterface::STATE_SOLD === $inventoryUnit->getInventoryState()) {
                --$onHand;
            }
        }

        $stockable->setOnHand($onHand);
    }
}
