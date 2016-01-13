<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Inventory\Factory\StockItemFactoryInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;

class StockItemContext extends DefaultContext
{
    /**
     * @Given there are stock locations for :productName in :location with :onHand on hand
     * @Given product :productName is available in :location with :onHand on hand
     */
    public function thereAreStockItems($productName, $location, $onHand)
    {
        $product = $this->findOneByName('product', $productName);
        /** @var StockLocationInterface $stockLocation */
        $stockLocation = $this->findOneByName('stock_location', $location);

        $this->thereAreStockItemsForVariantAndLocation($product->getMasterVariant(), $stockLocation, $onHand);

        $this->getEntityManager()->flush();
    }

    private function thereAreStockItemsForVariantAndLocation(StockableInterface $stockable, StockLocationInterface $location, $onHand)
    {
        /** @var StockItemRepositoryInterface $repository */
        $repository = $this->getRepository('stock_item');

        /** @var StockItemInterface $stockItem */
        if (null === $stockItem = $repository->findByStockableAndLocation($stockable, $location)) {
            $factory = $this->getFactory('stock_item');
            /** @var StockItemFactoryInterface $factory */
            $stockItem = $factory->createForLocation($stockable, $location);
        }

        $stockItem->setOnHand($onHand);

        $this->getEntityManager()->persist($stockItem);
    }
}
