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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductAssociationInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return ProductAssociationType
     */
    public function getType();

    /**
     * @param ProductAssociationTypeInterface $type
     */
    public function setType(ProductAssociationTypeInterface $type);

    /**
     * @return ProductInterface
     */
    public function getOwner();

    /**
     * @param ProductInterface|null $owner
     */
    public function setOwner(ProductInterface $owner = null);

    /**
     * @return Collection|ProductInterface[]
     */
    public function getAssociatedProducts();

    /**
     * @param ProductInterface $product
     */
    public function addAssociatedProduct(ProductInterface $product);

    /**
     * @param ProductInterface $product
     */
    public function removeAssociatedProduct(ProductInterface $product);

    /**
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasAssociatedProduct(ProductInterface $product);

    public function clearAssociatedProducts();
}
