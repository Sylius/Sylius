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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderItemInterface extends CartItemInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @return ProductVariantInterface
     */
    public function getVariant();

    /**
     * @param ProductVariantInterface $variant
     */
    public function setVariant(ProductVariantInterface $variant);

    /**
     * Returns the adjustments on it's child InventoryUnits
     *
     * @param string|null $type
     *
     * @return Adjustment[]
     */
    public function getAdjustments($type = null);

    /**
     * @param null $type
     * @param bool $includeNeutral
     *
     * @return int
     */
    public function calculateAdjustmentsTotal($type = null, $includeNeutral = false);

    /**
     * @return Collection|InventoryUnitInterface[]
     */
    public function getInventoryUnits();

    /**
     * @param InventoryUnitInterface $unit
     */
    public function addInventoryUnit(InventoryUnitInterface $unit);

    /**
     * @param InventoryUnitInterface $unit
     */
    public function removeInventoryUnit(InventoryUnitInterface $unit);

    /**
     * @param InventoryUnitInterface $unit
     *
     * @return bool
     */
    public function hasInventoryUnit(InventoryUnitInterface $unit);
}
