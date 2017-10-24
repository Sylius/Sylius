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

use Doctrine\Common\Collections\Collection;

interface ProductImageInterface extends ImageInterface
{
    /**
     * @return bool
     */
    public function hasProductVariants(): bool;

    /**
     * @return Collection|ProductVariantInterface[]
     */
    public function getProductVariants(): Collection;

    /**
     * @param ProductVariantInterface $productVariant
     */
    public function addProductVariant(ProductVariantInterface $productVariant): void;

    /**
     * @param ProductVariantInterface $productVariant
     */
    public function removeProductVariant(ProductVariantInterface $productVariant): void;

    /**
     * @param ProductVariantInterface $productVariant
     *
     * @return bool
     */
    public function hasProductVariant(ProductVariantInterface $productVariant): bool;
}
