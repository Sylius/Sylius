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

use Doctrine\Common\Collections\Collection;
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

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\ProductAssociation');
    }

    function it_extends_an_association()
    {
        $this->shouldHaveType('Sylius\Component\Association\Model\Association');
    }

    function it_implements_product_association_interface()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\AssociationInterface');
    }
}
