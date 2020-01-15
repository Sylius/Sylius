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

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

class ProductAssociation implements ProductAssociationInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var ProductAssociationTypeInterface */
    protected $type;

    /** @var ProductInterface */
    protected $owner;

    /**
     * @var Collection|ProductInterface[]
     *
     * @psalm-var Collection<array-key, ProductInterface>
     */
    protected $associatedProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        /** @var ArrayCollection<array-key, ProductInterface> $this->associatedProducts */
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
    public function getType(): ?ProductAssociationTypeInterface
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?ProductAssociationTypeInterface $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner(): ?ProductInterface
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(?ProductInterface $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociatedProducts(): Collection
    {
        return $this->associatedProducts;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociatedProduct(ProductInterface $product): bool
    {
        return $this->associatedProducts->contains($product);
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociatedProduct(ProductInterface $product): void
    {
        if (!$this->hasAssociatedProduct($product)) {
            $this->associatedProducts->add($product);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociatedProduct(ProductInterface $product): void
    {
        if ($this->hasAssociatedProduct($product)) {
            $this->associatedProducts->removeElement($product);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearAssociatedProducts(): void
    {
        $this->associatedProducts->clear();
    }
}
