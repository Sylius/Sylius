<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeValue;
use Sylius\Component\Attribute\Model\AttributeValueInterface;

final class AttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(AttributeValue::class);
    }

    function it_implements_Sylius_subject_attribute_interface(): void
    {
        $this->shouldImplement(AttributeValueInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_a_subject_by_default(): void
    {
        $this->getSubject()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_a_subject(AttributeSubjectInterface $subject): void
    {
        $this->setSubject($subject);
        $this->getSubject()->shouldReturn($subject);
    }

    function it_has_no_attribute_defined_by_default(): void
    {
        $this->getAttribute()->shouldReturn(null);
    }

    function its_attribute_is_definable(AttributeInterface $attribute): void
    {
        $this->setAttribute($attribute);
        $this->getAttribute()->shouldReturn($attribute);
    }

    function it_has_no_default_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn(null);
    }

    function its_locale_code_is_mutable(): void
    {
        $this->setLocaleCode('en');
        $this->getLocaleCode()->shouldReturn('en');
    }

    function it_has_no_value_by_default(): void
    {
        $this->getValue()->shouldReturn(null);
    }

    function its_value_is_mutable_based_on_attribute_storage_type(AttributeInterface $attribute): void
    {
        $storageTypeToExampleData = [
            'boolean' => false,
            'text' => 'Lorem ipsum',
            'integer' => 42,
            'float' => 6.66,
            'datetime' => new \DateTime(),
            'date' => new \DateTime(),
            'json' => ['foo' => 'bar'],
        ];

        foreach ($storageTypeToExampleData as $storageType => $exampleData) {
            $attribute->getStorageType()->willReturn($storageType);
            $this->setAttribute($attribute);

            $this->setValue($exampleData);
            $this->getValue()->shouldReturn($exampleData);
        }
    }

    function its_value_can_be_set_to_null(AttributeInterface $attribute): void
    {
        $storageTypes = [
            'boolean',
            'text',
            'integer',
            'float',
            'datetime',
            'date',
            'json',
        ];

        foreach ($storageTypes as $storageType) {
            $attribute->getStorageType()->willReturn($storageType);
            $this->setAttribute($attribute);

            $this->setValue(null);
            $this->getValue()->shouldReturn(null);
        }
    }

    function it_throws_exception_when_trying_to_get_code_without_attribute_defined(): void
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getCode')
        ;
    }

    function it_returns_its_attribute_code(AttributeInterface $attribute): void
    {
        $attribute->getCode()->willReturn('tshirt_material');
        $this->setAttribute($attribute);

        $this->getCode()->shouldReturn('tshirt_material');
    }

    function it_throws_exception_when_trying_to_get_name_without_attribute_defined(): void
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getName')
        ;
    }

    function it_returns_its_attribute_name(AttributeInterface $attribute): void
    {
        $attribute->getName()->willReturn('T-Shirt material');
        $this->setAttribute($attribute);

        $this->getName()->shouldReturn('T-Shirt material');
    }

    function it_throws_exception_when_trying_to_get_type_without_attribute_defined(): void
    {
        $this
            ->shouldThrow(\BadMethodCallException::class)
            ->during('getType')
        ;
    }

    function it_returns_its_attribute_type(AttributeInterface $attribute): void
    {
        $attribute->getType()->willReturn('choice');
        $this->setAttribute($attribute);

        $this->getType()->shouldReturn('choice');
    }
}
