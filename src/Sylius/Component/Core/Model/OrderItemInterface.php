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
    public function getImmutableProductName(): ?string;

    /**
     * @param string|null $immutableProductName
     */
    public function setImmutableProductName(?string $immutableProductName): void;

    /**
     * @return string|null
     */
    public function getImmutableProductCode(): ?string;

    /**
     * @param string|null $immutableProductCode
     */
    public function setImmutableProductCode(?string $immutableProductCode): void;

    /**
     * @return string|null
     */
    public function getImmutableVariantName(): ?string;

    /**
     * @param string|null $immutableVariantName
     */
    public function setImmutableVariantName(?string $immutableVariantName): void;

    /**
     * @return string|null
     */
    public function getImmutableVariantCode(): ?string;

    /**
     * @param string|null $immutableVariantCode
     */
    public function setImmutableVariantCode(?string $immutableVariantCode): void;

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
