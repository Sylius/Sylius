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
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeValueSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\AttributeValue');
    }

    public function it_extends_Sylius_attribute_value_model()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeValue');
    }

    public function it_implements_Sylius_product_attribute_value_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\AttributeValueInterface');
    }

    public function it_does_not_belong_to_a_product_by_default()
    {
        $this->getProduct()->shouldReturn(null);
    }

    public function it_allows_assigning_itself_to_a_product(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    public function it_allows_detaching_itself_from_a_product(ProductInterface $product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);

        $this->setProduct(null);
        $this->getProduct()->shouldReturn(null);
    }
}
