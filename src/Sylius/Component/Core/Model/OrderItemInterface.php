<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;

interface OrderItemInterface extends BaseOrderItemInterface
{
    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @return ProductVariantInterface|null
     */
    public function getVariant(): ?ProductVariantInterface;

    /**
     * @param ProductVariantInterface|null $variant
     */
    public function setVariant(?ProductVariantInterface $variant): void;

    /**
     * @return string|null
     */
    public function getProductName(): ?string;

    /**
     * @param string|null $productName
     */
    public function setProductName(?string $productName): void;

    /**
     * @return string|null
     */
    public function getVariantName(): ?string;

    /**
     * @param string|null $variantName
     */
    public function setVariantName(?string $variantName): void;

    /**
     * @return int
     */
    public function getTaxTotal(): int;

    /**
     * @return int
     */
    public function getDiscountedUnitPrice(): int;

    /**
     * @return int
     */
    public function getSubtotal(): int;
}
