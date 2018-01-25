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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ProductAssociationInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return ProductAssociationTypeInterface|null
     */
    public function getType(): ?ProductAssociationTypeInterface;

    /**
     * @param ProductAssociationTypeInterface|null $type
     */
    public function setType(?ProductAssociationTypeInterface $type): void;

    /**
     * @return ProductInterface|null
     */
    public function getOwner(): ?ProductInterface;

    /**
     * @param ProductInterface|null $owner
     */
    public function setOwner(?ProductInterface $owner): void;

    /**
     * @return Collection|ProductInterface[]
     */
    public function getAssociatedProducts(): Collection;

    /**
     * @param ProductInterface $product
     */
    public function addAssociatedProduct(ProductInterface $product): void;

    /**
     * @param ProductInterface $product
     */
    public function removeAssociatedProduct(ProductInterface $product): void;

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasAssociatedProduct(ProductInterface $product): bool;

    public function clearAssociatedProducts(): void;
}
