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
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductPropertyInterface;
use Sylius\Component\Product\Model\PropertyInterface;
use Sylius\Component\Product\Model\Property\Property;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $productPropertyRepository)
    {
        $this->beConstructedWith($productPropertyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Builder\PrototypeBuilder');
    }

    function it_implements_Sylius_prototype_builder_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Builder\PrototypeBuilderInterface');
    }

    function it_assigns_prototype_properties_to_product(
        $productPropertyRepository,
        PrototypeInterface$prototype,
        ProductInterface $product,
        PropertyInterface $property,
        ProductPropertyInterface $productProperty
    )
    {
        $prototype->getProperties()->willReturn(array($property))->shouldBeCalled();

        $productPropertyRepository->createNew()->shouldBeCalled()->willReturn($productProperty);
        $productProperty->setProperty($property)->shouldBeCalled();

        $product->addProperty($productProperty)->shouldBeCalled();

        $this->build($prototype, $product);
    }
}
