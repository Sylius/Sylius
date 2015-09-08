<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Coordinator;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;
use Sylius\Component\Inventory\Packaging\ItemsFactoryInterface;
use Sylius\Component\Inventory\Packaging\PackerInterface;
use Sylius\Component\Inventory\Provider\StockLocationProviderInterface;

/**
 * Coordinator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Coordinator implements CoordinatorInterface
{
    /**
     * Stock locations provider.
     *
     * @var StockLocationProviderInterface
     */
    protected $stockLocationProvider;

    /**
     * Inventory builder.
     *
     * @var InventoryBuilderInterface
     */
    protected $inventoryBuilder;

    /**
     * Packer.
     *
     * @var PackerInterface
     */
    protected $packer;

    /**
     * @param StockLocationProviderInterface $stockLocationProvider
     * @param ItemsFactoryInterface          $itemsFactory
     * @param PackerInterface                $packer
     */
    public function __construct(StockLocationProviderInterface $stockLocationProvider, PackerInterface $packer, ItemsFactoryInterface $itemsFactory)
    {
        $this->stockLocationProvider = $stockLocationProvider;
        $this->itemsFactory = $itemsFactory;
        $this->packer = $packer;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages(InventorySubjectInterface $subject)
    {
        $locations = $this->stockLocationProvider->getAvailableStockLocations($subject);
        $items = $this->itemsFactory->createItems($subject);
        $packages = array();

        foreach ($locations as $location) {
            foreach ($this->packer->pack($location, $items) as $package) {
                $packages[] = $package;
            }
        }

        return $packages;
    }
}
