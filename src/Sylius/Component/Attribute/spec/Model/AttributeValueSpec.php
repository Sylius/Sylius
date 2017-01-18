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
use Sylius\Component\Attribute\Model\AttributeValue;
use Sylius\Component\Attribute\Model\AttributeValueInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValue::class);
    }

    function it_implements_Sylius_subject_attribute_interface()
    {
        $this->shouldImplement(AttributeValueInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_a_subject_by_default()
    {
        $this->getSubject()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_a_subject(AttributeSubjectInterface $subject)
    {
        $this->setSubject($subject);
        $this->getSubject()->shouldReturn($subject);
    }

    function it_allows_detaching_itself_from_a_subject(AttributeSubjectInterface $subject)
    {
        $this->setSubject($subject);
        $this->getSubject()->shouldReturn($subject);

        $this->setSubject(null);
        $this->getSubject()->shouldReturn(null);
    }

    function it_has_no_attribute_defined_by_default()
    {
        $this->getAttribute()->shouldReturn(null);
    }

    function its_attribute_is_definable(AttributeInterface $attribute)
    {
        $this->setAttribute($attribute);
        $this->getAttribute()->shouldReturn($attribute);
    }

    function it_has_no_default_locale_code()
    {
        $this->getLocaleCode()->shouldReturn(null);
    }

    function its_locale_code_is_mutable()
    {
        $this->setLocaleCode('en');
        $this->getLocaleCode()->shouldReturn('en');
    }

    function it_has_no_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    function its_value_is_mutable_based_on_attribute_storage_type(AttributeInterface $attribute)
    {
        $attribute->getStorageType()->willReturn('text');
        $this->setAttribute($attribute);

        $this->setValue('XXL');
        $this->getValue()->shouldReturn('XXL');
    }

    function it_throws_exception_when_trying_to_get_code_without_attribute_defined()
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getCode')
        ;
    }

    function it_returns_its_attribute_code(AttributeInterface $attribute)
    {
        $attribute->getCode()->willReturn('tshirt_material');
        $this->setAttribute($attribute);

        $this->getCode()->shouldReturn('tshirt_material');
    }

    function it_throws_exception_when_trying_to_get_name_without_attribute_defined()
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getName')
        ;
    }

    function it_returns_its_attribute_name(AttributeInterface $attribute)
    {
        $attribute->getName()->willReturn('T-Shirt material');
        $this->setAttribute($attribute);

        $this->getName()->shouldReturn('T-Shirt material');
    }

    function it_throws_exception_when_trying_to_get_type_without_attribute_defined()
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getType')
        ;
    }

    function it_returns_its_attribute_type(AttributeInterface $attribute)
    {
        $attribute->getType()->willReturn('choice');
        $this->setAttribute($attribute);

        $this->getType()->shouldReturn('choice');
    }
}
