<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;
use Sylius\Component\Resource\Model\VersionedInterface;

interface OrderItemInterface extends BaseOrderItemInterface, VersionedInterface
{
    public function getProduct(): ?ProductInterface;

    public function getVariant(): ?ProductVariantInterface;

    public function setVariant(?ProductVariantInterface $variant): void;

    public function getProductName(): ?string;

    public function setProductName(?string $productName): void;

    public function getVariantName(): ?string;

    public function setVariantName(?string $variantName): void;

    public function getTaxTotal(): int;

    public function getDiscountedUnitPrice(): int;

    public function getSubtotal(): int;

    public function getFullDiscountedUnitPrice(): int;
}
