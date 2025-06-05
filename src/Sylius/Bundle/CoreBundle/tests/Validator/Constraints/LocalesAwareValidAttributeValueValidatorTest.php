<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Sylius\Bundle\CoreBundle\Validator\Constraints\LocalesAwareValidAttributeValueValidator;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class LocalesAwareValidAttributeValueValidatorTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $attributeTypeRegistry;

    private MockObject&TranslationLocaleProviderInterface $localeProvider;

    private ExecutionContextInterface&MockObject $context;

    private LocalesAwareValidAttributeValueValidator $validator;

    protected function setUp(): void
    {
        $this->attributeTypeRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->localeProvider = $this->createMock(TranslationLocaleProviderInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);

        $this->validator = new LocalesAwareValidAttributeValueValidator($this->attributeTypeRegistry, $this->localeProvider);
        $this->validator->initialize($this->context);
    }

    public function testItIsConstraintValidator(): void
    {
        $this->assertInstanceOf(LocalesAwareValidAttributeValueValidator::class, $this->validator);
    }

    public function testItValidatesAttributeBasedOnItsTypeAndSetItAsRequiredIfLocaleMatchesDefault(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attributeValue = $this->createMock(AttributeValueInterface::class);
        $attributeType = $this->createMock(AttributeTypeInterface::class);
        $constraint = new ValidAttributeValue();

        $attributeValue->method('getType')->willReturn(TextAttributeType::TYPE);
        $this->attributeTypeRegistry->method('get')->with(TextAttributeType::TYPE)->willReturn($attributeType);
        $attributeValue->method('getAttribute')->willReturn($attribute);
        $attribute->method('getConfiguration')->willReturn(['min' => 2, 'max' => 255]);

        $this->localeProvider->method('getDefaultLocaleCode')->willReturn('en_US');
        $attributeValue->method('getLocaleCode')->willReturn('en_US');

        $attributeType
            ->expects($this->once())
            ->method('validate')
            ->with($attributeValue, $this->context, ['min' => 2, 'max' => 255, 'required' => true])
        ;

        $this->validator->validate($attributeValue, $constraint);
    }

    public function testItValidatesAttributeValueBasedOnItsTypeAndDoNotSetRequiredIfLocaleDoesNotMatch(): void
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attributeValue = $this->createMock(AttributeValueInterface::class);
        $attributeType = $this->createMock(AttributeTypeInterface::class);
        $constraint = new ValidAttributeValue();

        $attributeValue->method('getType')->willReturn(TextAttributeType::TYPE);
        $this->attributeTypeRegistry->method('get')->with(TextAttributeType::TYPE)->willReturn($attributeType);
        $attributeValue->method('getAttribute')->willReturn($attribute);
        $attribute->method('getConfiguration')->willReturn(['min' => 2, 'max' => 255]);

        $this->localeProvider->method('getDefaultLocaleCode')->willReturn('en_US');
        $attributeValue->method('getLocaleCode')->willReturn('pl');

        $attributeType
            ->expects($this->once())
            ->method('validate')
            ->with($attributeValue, $this->context, ['min' => 2, 'max' => 255])
        ;

        $this->validator->validate($attributeValue, $constraint);
    }

    public function testItThrowsExceptionIfValidatedValueIsNotAttributeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $constraint = new ValidAttributeValue();

        $this->validator->validate(new \DateTime(), $constraint);
    }
}
