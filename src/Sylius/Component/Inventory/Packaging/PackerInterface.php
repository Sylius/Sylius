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

use Sylius\Component\Inventory\Model\StockLocationInterface;

/**
 * Packer is responsible for packaging inventory for every stockable and stock location.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PackerInterface
{
    /**
     * Obtain a collection of packages with given set of inventory units for particular location.
     *
     * @param StockLocationInterface   $stockLocation
     * @param Items                    $items
     *
     * @return PackageInterface[]
     */
    public function pack(StockLocationInterface $stockLocation, Items $items);
}
