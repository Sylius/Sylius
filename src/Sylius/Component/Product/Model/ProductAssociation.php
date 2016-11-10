<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductAssociation implements ProductAssociationInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var ProductAssociationTypeInterface
     */
    protected $type;

    /**
     * @var ProductInterface
     */
    protected $owner;

    /**
     * @var Collection|ProductInterface[]
     */
    protected $associatedProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->associatedProducts = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(ProductAssociationTypeInterface $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(ProductInterface $owner = null)
    {
        $this->owner = $owner;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociatedProducts()
    {
        return $this->associatedProducts;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociatedProduct(ProductInterface $product)
    {
        return $this->associatedProducts->contains($product);
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociatedProduct(ProductInterface $product)
    {
        if (!$this->hasAssociatedProduct($product)) {
            $this->associatedProducts->add($product);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociatedProduct(ProductInterface $product)
    {
        if ($this->hasAssociatedProduct($product)) {
            $this->associatedProducts->removeElement($product);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearAssociatedProducts()
    {
        $this->associatedProducts->clear();
    }
}
