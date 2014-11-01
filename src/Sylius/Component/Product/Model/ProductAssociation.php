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

/**
 * ProductAssociation model.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProductAssociation extends Association
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param ProductInterface $product
     * @param AssociationType $type
     */
    public function __construct(ProductInterface $product, AssociationType $type)
    {
        parent::__construct($type);
        $this->product = $product;
    }

    /**
     * @return ProductInterface
     */
    final public function getAssociatedObject()
    {
        return $this->product;
    }
}
