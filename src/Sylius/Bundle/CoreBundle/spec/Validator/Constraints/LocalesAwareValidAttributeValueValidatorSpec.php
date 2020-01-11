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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LocalesAwareValidAttributeValueValidatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $attributeTypesRegistry, ExecutionContextInterface $context, TranslationLocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($attributeTypesRegistry, $localeProvider);
        $this->initialize($context);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_attribute_based_on_its_type_and_set_it_as_required_if_its_locale_is_same_as_default_locale(
        AttributeInterface $attribute,
        AttributeTypeInterface $attributeType,
        AttributeValueInterface $attributeValue,
        ServiceRegistryInterface $attributeTypesRegistry,
        ValidAttributeValue $attributeValueConstraint,
        TranslationLocaleProviderInterface $localeProvider
    ): void {
        $attributeValue->getType()->willReturn(TextAttributeType::TYPE);
        $attributeTypesRegistry->get('text')->willReturn($attributeType);
        $attributeValue->getAttribute()->willReturn($attribute);
        $attribute->getConfiguration()->willReturn(['min' => 2, 'max' => 255]);

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $attributeValue->getLocaleCode()->willReturn('pl');

        $attributeType->validate($attributeValue, Argument::type(ExecutionContextInterface::class), ['min' => 2, 'max' => 255])->shouldBeCalled();

        $this->validate($attributeValue, $attributeValueConstraint);
    }

    function it_validates_attribute_value_based_on_its_type_and_do_not_set_it_as_required_if_its_locale_is_not_same_as_default_locale(
        AttributeInterface $attribute,
        AttributeTypeInterface $attributeType,
        AttributeValueInterface $attributeValue,
        ServiceRegistryInterface $attributeTypesRegistry,
        ValidAttributeValue $attributeValueConstraint,
        TranslationLocaleProviderInterface $localeProvider
    ): void {
        $attributeValue->getType()->willReturn(TextAttributeType::TYPE);
        $attributeTypesRegistry->get('text')->willReturn($attributeType);
        $attributeValue->getAttribute()->willReturn($attribute);
        $attribute->getConfiguration()->willReturn(['min' => 2, 'max' => 255]);

        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $attributeValue->getLocaleCode()->willReturn('en_US');

        $attributeType->validate($attributeValue, Argument::type(ExecutionContextInterface::class), ['min' => 2, 'max' => 255, 'required' => true])->shouldBeCalled();

        $this->validate($attributeValue, $attributeValueConstraint);
    }

    function it_throws_exception_if_validated_value_is_not_attribute_value(
        \DateTime $badObject,
        ValidAttributeValue $attributeValueConstraint
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$badObject, $attributeValueConstraint])
        ;
    }
}
