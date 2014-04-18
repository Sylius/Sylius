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
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Order item interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends CartItemInterface, PromotionSubjectInterface
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
     * @return Collection|InventoryUnitInterface[]
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

    /**
     * Get all promotion adjustments.
     *
     * @return Collection|AdjustmentInterface[]
     */
    public function getPromotionAdjustments();

    /**
     * Remove all promotion adjustments.
     */
    public function removePromotionAdjustments();
}
