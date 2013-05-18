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
use Sylius\Bundle\ProductBundle\Model\Property\Property;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilderSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $productPropertyRepository
     */
    function let($productPropertyRepository)
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

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PrototypeInterface       $prototype
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface         $product
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface        $property
     * @param Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface $productProperty
     */
    function it_assigns_prototype_properties_to_product(
        $productPropertyRepository, $prototype, $product, $property, $productProperty
    )
    {
        $prototype->getProperties()->willReturn(array($property))->shouldBeCalled();

        $productPropertyRepository->createNew()->shouldBeCalled()->willReturn($productProperty);
        $productProperty->setProperty($property)->shouldBeCalled();

        $product->addProperty($productProperty)->shouldBeCalled();

        $this->build($prototype, $product);
    }
}
