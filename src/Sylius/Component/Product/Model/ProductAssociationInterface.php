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

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ProductAssociationInterface extends TimestampableInterface, ResourceInterface
{
    public function getType(): ?ProductAssociationTypeInterface;

    public function setType(?ProductAssociationTypeInterface $type): void;

    public function getOwner(): ?ProductInterface;

    public function setOwner(?ProductInterface $owner): void;

    /**
     * @return Collection<array-key, ProductInterface>
     */
    public function getAssociatedProducts(): Collection;

    public function addAssociatedProduct(ProductInterface $product): void;

    public function removeAssociatedProduct(ProductInterface $product): void;

    public function hasAssociatedProduct(ProductInterface $product): bool;

    public function clearAssociatedProducts(): void;
}
