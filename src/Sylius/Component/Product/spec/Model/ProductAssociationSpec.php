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
use Prophecy\Argument;
use Sylius\Component\Association\Model\AssociationType;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductAssociationSpec extends ObjectBehavior
{
    function let(ProductInterface $product, AssociationType $associationType)
    {
        $this->beConstructedWith($product, $associationType);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\ProductAssociation');
    }

    function it_extends_abstract_association()
    {
        $this->shouldHaveType('Sylius\Component\Association\Model\AbstractAssociation');
    }

    function it_implements_association_interface()
    {
        $this->shouldHaveType('Sylius\Component\Association\Model\AssociationInterface');
    }

    function it_has_associated_object($product)
    {
        $this->getAssociatedObject()->shouldReturn($product);
    }

    function its_associated_object_is_mutable(ProductInterface $newProduct)
    {
        $this->setAssociatedObject($newProduct);
        $this->getAssociatedObject()->shouldReturn($newProduct);
    }
}
