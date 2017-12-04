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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ProductImage extends Image implements ProductImageInterface
{
    /**
     * @var Collection|ProductVariantInterface[]
     */
    protected $productVariants;

    public function __construct()
    {
        $this->productVariants = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductVariants(): bool
    {
        return !$this->productVariants->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductVariant(ProductVariantInterface $productVariant): bool
    {
        return $this->productVariants->contains($productVariant);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->productVariants->add($productVariant);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductVariant(ProductVariantInterface $productVariant): void
    {
        if ($this->hasProductVariant($productVariant)) {
            $this->productVariants->removeElement($productVariant);
        }
    }
}
