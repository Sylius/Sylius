<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeTypes;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeValueSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeValue');
    }

    public function it_implements_Sylius_subject_attribute_interface()
    {
        $this->shouldImplement('Sylius\Component\Attribute\Model\AttributeValueInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_does_not_belong_to_a_subject_by_default()
    {
        $this->getSubject()->shouldReturn(null);
    }

    public function it_allows_assigning_itself_to_a_subject(AttributeSubjectInterface $subject)
    {
        $this->setSubject($subject);
        $this->getSubject()->shouldReturn($subject);
    }

    public function it_allows_detaching_itself_from_a_subject(AttributeSubjectInterface $subject)
    {
        $this->setSubject($subject);
        $this->getSubject()->shouldReturn($subject);

        $this->setSubject(null);
        $this->getSubject()->shouldReturn(null);
    }

    public function it_has_no_attribute_defined_by_default()
    {
        $this->getAttribute()->shouldReturn(null);
    }

    public function its_attribute_is_definable(AttributeInterface $attribute)
    {
        $this->setAttribute($attribute);
        $this->getAttribute()->shouldReturn($attribute);
    }

    public function it_has_no_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    public function its_value_is_mutable()
    {
        $this->setValue('XXL');
        $this->getValue()->shouldReturn('XXL');
    }

    public function it_converts_value_to_Boolean_if_attribute_has_checkbox_type(AttributeInterface $attribute)
    {
        $attribute->getType()->willReturn(AttributeTypes::CHECKBOX);
        $this->setAttribute($attribute);

        $this->setValue('1');
        $this->getValue()->shouldReturn(true);

        $this->setValue(0);
        $this->getValue()->shouldReturn(false);
    }

    public function it_returns_its_value_when_converted_to_string()
    {
        $this->setValue('S');
        $this->__toString()->shouldReturn('S');
    }

    public function it_throws_exception_when_trying_to_get_name_without_attribute_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetName()
        ;
    }

    public function it_returns_its_attribute_name(AttributeInterface $attribute)
    {
        $attribute->getName()->willReturn('T-Shirt material');
        $this->setAttribute($attribute);

        $this->getName()->shouldReturn('T-Shirt material');
    }

    public function it_throws_exception_when_trying_to_get_presentation_without_attribute_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetPresentation()
        ;
    }

    public function it_returns_its_attribute_presentation(AttributeInterface $attribute)
    {
        $attribute->getPresentation()->willReturn('Material');
        $this->setAttribute($attribute);

        $this->getPresentation()->shouldReturn('Material');
    }

    public function it_throws_exception_when_trying_to_get_type_without_attribute_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetType()
        ;
    }

    public function it_returns_its_attribute_type(AttributeInterface $attribute)
    {
        $attribute->getType()->willReturn('choice');
        $this->setAttribute($attribute);

        $this->getType()->shouldReturn('choice');
    }

    public function it_throws_exception_when_trying_to_get_configuration_without_attribute_defined()
    {
        $this
            ->shouldThrow('BadMethodCallException')
            ->duringGetConfiguration()
        ;
    }

    public function it_returns_its_attribute_configuration(AttributeInterface $attribute)
    {
        $attribute->getConfiguration()->willReturn(array('choices' => array('Red')));
        $this->setAttribute($attribute);

        $this->getConfiguration()->shouldReturn(array('choices' => array('Red')));
    }
}
