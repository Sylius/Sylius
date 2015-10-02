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
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

class StockItemContext extends DefaultContext
{
    /**
     * @Given there are stock locations for :productName in :location with :onHand on hand
     * @Given product :productName is available in :location with :onHand on hand
     */
    public function thereAreStockItems($productName, $location, $onHand)
    {
        $product = $this->findOneByName('product', $productName);
        $stockLocation = $this->findOneByName('stock_location', $location);

        foreach ($product->getVariants() as $variant) {
            $this->thereAreStockItemsForVariantAndLocation($variant, $stockLocation, $onHand);
        }

        $this->getEntityManager()->flush();
    }

    private function thereAreStockItemsForVariantAndLocation(StockableInterface $stockable, StockLocationInterface $location, $onHand)
    {
        $stockItem = $this->getService('sylius.factory.stock_item')
            ->create($stockable, $location)
            ->setOnHand($onHand)
        ;

        $this->getEntityManager()->persist($stockItem);
    }
}
