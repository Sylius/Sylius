<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PrototypeBuilderSpec extends ObjectBehavior
{
    function let(DomainManagerInterface $attributeValueManager)
    {
        $this->beConstructedWith($attributeValueManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Builder\PrototypeBuilder');
    }

    function it_implements_Sylius_prototype_builder_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Builder\PrototypeBuilderInterface');
    }

    function it_assigns_prototype_attributes_and_options_to_product(
        $attributeValueManager,
        PrototypeInterface$prototype,
        ProductInterface $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        OptionInterface $option
    ) {
        $prototype->getAttributes()->willReturn(array($attribute));
        $prototype->getOptions()->willReturn(array($option));

        $attributeValueManager->createNew()->willReturn($attributeValue);
        $attributeValue->setAttribute($attribute)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();
        $product->addOption($option)->shouldBeCalled();

        $this->build($prototype, $product);
    }
}
