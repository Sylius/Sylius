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
use Doctrine\Common\Collections\Collection;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductImage extends Image implements ProductImageInterface
{
    /**
     * @var Collection|ProductVariantInterface[]
     */
    protected $productVariants;

    public function __construct()
    {
        parent::__construct();

        $this->productVariants = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductVariants()
    {
        return !$this->productVariants->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductVariants()
    {
        return $this->productVariants;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProductVariant(ProductVariantInterface $productVariant)
    {
        return $this->productVariants->contains($productVariant);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductVariant(ProductVariantInterface $productVariant)
    {
        $this->productVariants->add($productVariant);
    }

    /**
     * {@inheritdoc}
     */
    public function removeProductVariant(ProductVariantInterface $productVariant)
    {
        if ($this->hasProductVariant($productVariant)) {
            $this->productVariants->removeElement($productVariant);
        }
    }
}
