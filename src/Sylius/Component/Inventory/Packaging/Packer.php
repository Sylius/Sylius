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

use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
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
    public function pack(StockLocationInterface $stockLocation, Items $items)
    {
        $package = $this->packageFactory->create($stockLocation);

        foreach ($items->getStockables() as $stockable) {
            $stockItem = $this->stockItemRepository->findByStockableAndLocation($stockable, $stockLocation);

            if (null === $stockItem) {
                continue;
            }

            $available = $stockItem->getOnHand() - $stockItem->getOnHold();

            if (0 === $available && !$stockItem->isAvailableOnDemand()) {
                continue;
            }

            $remaining = $items->getRemaining($stockable);

            for ($i = 0; $i < $remaining; $i++) {
                $unit = $items->getInventoryUnitForPacking($stockable);

                if (null === $unit) {
                    break;
                }

                $unit->setStockItem($stockItem);

                $package->addInventoryUnit($unit);
            }
        }

        if ($package->isEmpty()) {
            return array();
        }

        $packages = array($package);

        foreach ($this->splitters as $splitter) {
            $packages = $splitter->split($packages);
        }

        return $packages;
    }
}
