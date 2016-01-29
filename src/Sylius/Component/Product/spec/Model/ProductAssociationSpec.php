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
use Sylius\Component\Association\Model\Association;
use Sylius\Component\Product\Model\ProductAssociationInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductAssociationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\ProductAssociation');
    }

    function it_extends_an_association()
    {
        $this->shouldHaveType(Association::class);
    }

    function it_implements_product_association_interface()
    {
        $this->shouldHaveType(ProductAssociationInterface::class);
    }
}
