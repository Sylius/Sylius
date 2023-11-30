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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductImage extends Image implements ProductImageInterface
{
    /**
     * @var Collection|ProductVariantInterface[]
     *
     * @psalm-var Collection<array-key, ProductVariantInterface>
     */
    protected $productVariants;

    public function __construct()
    {
        /** @var ArrayCollection<array-key, ProductVariantInterface> $this->productVariants */
        $this->productVariants = new ArrayCollection();
    }

    public function hasProductVariants(): bool
    {
        return !$this->productVariants->isEmpty();
    }

    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    public function hasProductVariant(ProductVariantInterface $productVariant): bool
    {
        return $this->productVariants->contains($productVariant);
    }

    public function addProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariants->add($productVariant);
    }

    public function removeProductVariant(ProductVariantInterface $productVariant): void
    {
        if ($this->hasProductVariant($productVariant)) {
            $this->productVariants->removeElement($productVariant);
        }
    }
}
