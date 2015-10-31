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
 * Default package factory.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PackageFactory implements PackageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(StockLocationInterface $stockLocation)
    {
        return new Package($stockLocation);
    }
}
