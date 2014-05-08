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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Cart\Model\CartItemInterface;

/**
 * Order item interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends CartItemInterface
{
    /**
     * Get the product.
     *
     * @return ProductInterface
     */
    public function getProduct();

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

    /**
     * Get all inventory units.
     *
     * @return ArrayCollection|InventoryUnitInterface[]
     */
    public function getInventoryUnits();

    /**
     * Add inventory unit.
     *
     * @param InventoryUnitInterface $unit
     */
    public function addInventoryUnit(InventoryUnitInterface $unit);

    /**
     * Remove inventory unit.
     *
     * @param InventoryUnitInterface $unit
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit);

    /**
     * Has inventory unit?
     *
     * @param InventoryUnitInterface $unit
     *
     * @return Boolean
     */
    public function hasInventoryUnit(InventoryUnitInterface $unit);
}
