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

namespace Sylius\Behat\Element\Admin\Product;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;

interface AssociationsFormElementInterface
{
    /**
     * @param string[] $productsNames
     */
    public function associateProducts(ProductAssociationTypeInterface $productAssociationType, array $productsNames): void;

    public function removeAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): void;

    public function hasAssociatedProduct(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): bool;
}
