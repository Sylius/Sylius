<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface;
use Sylius\Bundle\ProductBundle\Model\Property\Property;
use Sylius\Bundle\ProductBundle\Model\PropertyInterface;
use Sylius\Bundle\ProductBundle\Model\PrototypeInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

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
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Builder\PrototypeBuilder');
    }

    function it_implements_Sylius_prototype_builder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ProductBundle\Builder\PrototypeBuilderInterface');
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
