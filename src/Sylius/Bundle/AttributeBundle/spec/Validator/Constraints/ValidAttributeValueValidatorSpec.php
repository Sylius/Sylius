<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValueValidator;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ValidAttributeValueValidatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $attributeTypesRegistry, ExecutionContextInterface $context)
    {
        $this->beConstructedWith($attributeTypesRegistry);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ValidAttributeValueValidator::class);
    }

    function it_is_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_attribute_value_based_on_their_type(
        AttributeInterface $attribute,
        AttributeTypeInterface $attributeType,
        AttributeValueInterface $attributeValue,
        ServiceRegistryInterface $attributeTypesRegistry,
        ValidAttributeValue $attributeValueConstraint
    ) {
        $attributeValue->getType()->willReturn(TextAttributeType::TYPE);
        $attributeTypesRegistry->get('text')->willReturn($attributeType);
        $attributeValue->getAttribute()->willReturn($attribute);
        $attribute->getConfiguration()->willReturn(['min' => 2, 'max' => 255]);

        $attributeType->validate($attributeValue, Argument::any(ExecutionContextInterface::class), ['min' => 2, 'max' => 255])->shouldBeCalled();

        $this->validate($attributeValue, $attributeValueConstraint);
    }

    function it_throws_exception_if_validated_value_is_not_attribute_value(
        \DateTime $badObject,
        ValidAttributeValue $attributeValueConstraint
    ) {
        $this
            ->shouldThrow(new UnexpectedTypeException('\DateTime', AttributeValueInterface::class))
            ->during('validate', [$badObject, $attributeValueConstraint])
        ;
    }
}
