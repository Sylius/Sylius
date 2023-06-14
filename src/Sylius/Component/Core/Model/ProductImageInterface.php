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

use Doctrine\Common\Collections\Collection;

interface ProductImageInterface extends ImageInterface
{
    public function hasProductVariants(): bool;

    /**
     * @return Collection|ProductVariantInterface[]
     *
     * @psalm-return Collection<array-key, ProductVariantInterface>
     */
    public function getProductVariants(): Collection;

    public function addProductVariant(ProductVariantInterface $productVariant): void;

    public function removeProductVariant(ProductVariantInterface $productVariant): void;

    public function hasProductVariant(ProductVariantInterface $productVariant): bool;
}
