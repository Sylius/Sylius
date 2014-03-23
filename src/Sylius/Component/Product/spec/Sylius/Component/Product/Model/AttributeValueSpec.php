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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductAttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\ProductAttributeValue');
    }

    function it_extends_Sylius_attribute_value_model()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeValue');
    }

    function it_implements_Sylius_product_attribute_value_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\ProductAttributeValueInterface');
    }

    function it_does_not_belong_to_a_product_by_default()
    {
        $this->getProduct()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_a_product(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    function it_allows_detaching_itself_from_a_product(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);

        $this->setProduct(null);
        $this->getProduct()->shouldReturn(null);
    }
}
