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

use Sylius\Component\Association\Model\AbstractAssociation;
use Sylius\Component\Association\Model\AssociationInterface;
use Sylius\Component\Association\Model\AssociationTypeInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductAssociation extends AbstractAssociation implements AssociationInterface
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param ProductInterface $product
     * @param AssociationTypeInterface $type
     */
    public function __construct(ProductInterface $product, AssociationTypeInterface $type)
    {
        parent::__construct($type);
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     *
     * @return ProductInterface
     */
    public function getAssociatedObject()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     */
    public function setAssociatedObject(ProductInterface $product)
    {
        $this->product = $product;
    }
}
