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
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $attributeValueRepository)
    {
        $this->beConstructedWith($attributeValueRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Builder\PrototypeBuilder');
    }

    function it_implements_Sylius_prototype_builder_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Builder\PrototypeBuilderInterface');
    }

    function it_assigns_prototype_attributes_to_product(
        $attributeValueRepository,
        PrototypeInterface$prototype,
        ProductInterface $product,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue
    )
    {
        $prototype->getAttributes()->willReturn(array($attribute))->shouldBeCalled();

        $attributeValueRepository->createNew()->shouldBeCalled()->willReturn($attributeValue);
        $attributeValue->setAttribute($attribute)->shouldBeCalled();

        $product->addAttribute($attributeValue)->shouldBeCalled();

        $this->build($prototype, $product);
    }
}
