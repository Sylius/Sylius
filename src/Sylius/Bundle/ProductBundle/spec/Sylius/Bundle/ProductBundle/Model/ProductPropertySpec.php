<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Model\PropertyTypes;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductPropertySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Model\ProductProperty');
    }

    function it_implements_Sylius_product_property_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ProductBundle\Model\ProductPropertyInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_a_product_by_default()
    {
        $this->getProduct()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_allows_assigning_itself_to_a_product($product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\ProductInterface $product
     */
    function it_allows_detaching_itself_from_a_product($product)
    {
        $this->setProduct($product);
        $this->getProduct()->shouldReturn($product);

        $this->setProduct(null);
        $this->getProduct()->shouldReturn(null);
    }

    function it_has_no_property_defined_by_default()
    {
        $this->getProperty()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function its_property_is_definable($property)
    {
        $this->setProperty($property);
        $this->getProperty()->shouldReturn($property);
    }

    function it_has_no_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    function its_value_is_mutable()
    {
        $this->setValue('XXL');
        $this->getValue()->shouldReturn('XXL');
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function it_converts_value_to_Boolean_if_property_has_checkbox_type($property)
    {
        $property->getType()->willReturn(PropertyTypes::CHECKBOX);
        $this->setProperty($property);

        $this->setValue('1');
        $this->getValue()->shouldReturn(true);

        $this->setValue(0);
        $this->getValue()->shouldReturn(false);
    }

    function it_returns_its_value_when_converted_to_string()
    {
        $this->setValue('S');
        $this->__toString()->shouldReturn('S');
    }

    function it_throws_exception_when_trying_to_get_name_without_property_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetName()
        ;
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function it_returns_its_property_name($property)
    {
        $property->getName()->willReturn('T-Shirt material');
        $this->setProperty($property);

        $this->getName()->shouldReturn('T-Shirt material');
    }

    function it_throws_exception_when_trying_to_get_presentation_without_property_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetPresentation()
        ;
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function it_returns_its_property_presentation($property)
    {
        $property->getPresentation()->willReturn('Material');
        $this->setProperty($property);

        $this->getPresentation()->shouldReturn('Material');
    }

    function it_throws_exception_when_trying_to_get_type_without_property_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetType()
        ;
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function it_returns_its_property_type($property)
    {
        $property->getType()->willReturn('choice');
        $this->setProperty($property);

        $this->getType()->shouldReturn('choice');
    }

    function it_throws_exception_when_trying_to_get_configuration_without_property_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetConfiguration()
        ;
    }

    /**
     * @param Sylius\Bundle\ProductBundle\Model\PropertyInterface $property
     */
    function it_returns_its_property_configuration($property)
    {
        $property->getConfiguration()->willReturn(array('choices' => array('Red')));
        $this->setProperty($property);

        $this->getConfiguration()->shouldReturn(array('choices' => array('Red')));
    }
}
