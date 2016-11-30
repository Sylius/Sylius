<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAssociation;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationType;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductAssociationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductAssociation::class);
    }

    function it_implements_ProductAssociation_interface()
    {
        $this->shouldHaveType(ProductAssociationInterface::class);
    }

    function it_has_owner(ProductInterface $product)
    {
        $this->setOwner($product);
        $this->getOwner()->shouldReturn($product);
    }

    function it_has_type(ProductAssociationType $associationType)
    {
        $this->setType($associationType);
        $this->getType()->shouldReturn($associationType);
    }

    function it_adds_association_product(ProductInterface $product)
    {
        $this->addAssociatedProduct($product);
        $this->getAssociatedProducts()->shouldHaveCount(1);
    }

    function it_checks_if_product_is_associated(ProductInterface $product)
    {
        $this->hasAssociatedProduct($product)->shouldReturn(false);

        $this->addAssociatedProduct($product);
        $this->hasAssociatedProduct($product)->shouldReturn(true);

        $this->removeAssociatedProduct($product);
        $this->hasAssociatedProduct($product)->shouldReturn(false);
    }
}
