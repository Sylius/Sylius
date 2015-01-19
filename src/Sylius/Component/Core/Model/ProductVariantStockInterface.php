<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Inventory\Model\StockInterface as BaseStockInterface;

/**
 * Sylius core inventory manager interface.
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface ProductVariantStockInterface extends BaseStockInterface 
{
    /**
     * Get variant.
     *
     * @return ProductVariantInterface
     */
    public function getVariant();

    /**
     * Set variant.
     *
     * @param ProductVariantInterface $variant
     */
    public function setVariant(ProductVariantInterface $variant);

}
