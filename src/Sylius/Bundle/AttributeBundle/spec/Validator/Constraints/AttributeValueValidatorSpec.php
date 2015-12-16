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
use Sylius\Bundle\AttributeBundle\AttributeType\TextAttributeType;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeValue;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AttributeValueValidatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $attributeTypesRegistry, ExecutionContextInterface $context)
    {
        $this->beConstructedWith($attributeTypesRegistry);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeValueValidator');
    }
    
    function it_is_constraint_validator()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_validates_attribute_value_based_on_their_type(
        $attributeTypesRegistry,
        AttributeTypeInterface $attributeType,
        AttributeValueInterface $attributeValue,
        AttributeValue $attributeValueConstraint
    ) {
        $attributeValue->getType()->willReturn(TextAttributeType::TYPE);
        $attributeTypesRegistry->get('text')->willReturn($attributeType);

        $attributeType->validate($attributeValue, Argument::any('Symfony\Component\Validator\Context\ExecutionContextInterface'))->shouldBeCalled();

        $this->validate($attributeValue, $attributeValueConstraint);
    }
}
