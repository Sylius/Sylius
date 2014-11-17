<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Packaging;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Packaging\Splitter\SplitterInterface;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;

/**
 * Default packer implementation.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Packer implements PackerInterface
{
    /**
     * @var PackageFactoryInterface
     */
    protected $packageFactory;

    /**
     * @var StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var SplitterInterface[]
     */
    protected $splitters;

    /**
     * @param PackageFactoryInterface      $packageFactory
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param SplitterInterface[]          $splitters
     */
    public function __construct(PackageFactoryInterface $packageFactory, StockItemRepositoryInterface $stockItemRepository, array $splitters = array())
    {
        $this->packageFactory = $packageFactory;
        $this->stockItemRepository = $stockItemRepository;

        foreach ($splitters as $splitters) {
            if (!$splitter instanceof SplitterInterface) {
                throw new \InvalidArgumentException(sprintf('Expected instance of "Sylius\Component\Inventory\Packaging\Splitter\SplitterInterface", "%s" given.', is_object($splitter) ? get_class($splitter) : gettype($splitter)));
            }
        }

        $this->splitters = $splitters;
    }

    /**
     * {@inheritdoc}
     */
    public function pack(StockLocationInterface $stockLocation, Collection $inventoryUnits)
    {
        $items = array();
        $package = $this->packageFactory->create($stockLocation);

        foreach ($inventoryUnits as $unit) {
            if (!$inventoryUnit instanceof InventoryUnitInterface) {
                throw new \InvalidArgumentException(sprintf('Expected instance of "Sylius\Component\Inventory\Model\InventoryUnitInterface", "%s" given.', is_object($splitter) ? get_class($splitter) : gettype($splitter)));
            }

            $stockable = $unit->getStockable();
            $id = spl_object_hash($stockable);

            $items[$id]['stockable'] = $stockable;
            $items[$id]['units'][] = $unit;
        }

        foreach ($items as $item) {
            $stockable = $item['stockable'];
            $stockItem = $stockItemRepository->findOneByLocationAndStockable($stockLocation, $stockable);

            if (null === $stockItem) {
                continue;
            }

            $available = $stockItem->getOnHand() - $stockItem->getOnHold();

            foreach ($item['units'] as $inventoryUnit) {
                $package->addInventoryUnit($inventoryUnit);
            }
        }

        $packages = array($package);

        foreach ($this->splitters as $splitter) {
            $packages = $splitter->split($packages);
        }

        return $packages;
    }
}
