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
use Sylius\Component\Product\Model\AssociationType;
use Sylius\Component\Product\Model\ProductInterface;

class ProductAssociationSpec extends ObjectBehavior
{
    function let(ProductInterface $product, AssociationType $associationType)
    {
        $this->beConstructedWith($product, $associationType);
    }

    function it_is_association()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Association');
    }

    function it_allows_to_get_associated_product(ProductInterface $product)
    {
        $this->getAssociatedObject()->shouldBe($product);
    }
}
