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

namespace Tests\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValueValidator;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ValidAttributeValueValidatorTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $attributeTypesRegistry;

    private ExecutionContextInterface&MockObject $context;

    private ValidAttributeValueValidator $validAttributeValueValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attributeTypesRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validAttributeValueValidator = new ValidAttributeValueValidator($this->attributeTypesRegistry);
        $this->initialize($this->context);
    }

    private function initialize(ExecutionContextInterface $context): void
    {
        $this->validAttributeValueValidator->initialize($context);
    }

    public function testValidatesAttributeValueBasedOnTheirType(): void
    {
        /** @var AttributeInterface&MockObject $attributeMock */
        $attributeMock = $this->createMock(AttributeInterface::class);
        /** @var AttributeTypeInterface&MockObject $attributeTypeMock */
        $attributeTypeMock = $this->createMock(AttributeTypeInterface::class);
        /** @var AttributeValueInterface&MockObject $attributeValueMock */
        $attributeValueMock = $this->createMock(AttributeValueInterface::class);
        /** @var ValidAttributeValue&MockObject $attributeValueConstraintMock */
        $attributeValueConstraintMock = $this->createMock(ValidAttributeValue::class);

        $attributeValueMock->expects(self::once())
            ->method('getType')
            ->willReturn(TextAttributeType::TYPE);

        $this->attributeTypesRegistry->expects(self::once())
            ->method('get')
            ->with('text')
            ->willReturn($attributeTypeMock);

        $attributeValueMock->expects(self::once())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $attributeMock->expects(self::once())
            ->method('getConfiguration')
            ->willReturn(['min' => 2, 'max' => 255]);

        $attributeTypeMock->expects(self::once())
            ->method('validate')
            ->with(
                $attributeValueMock,
                $this->isInstanceOf(ExecutionContextInterface::class),
                [
                    'min' => 2,
                    'max' => 255,
                ],
            );

        $this->validAttributeValueValidator->validate($attributeValueMock, $attributeValueConstraintMock);
    }

    public function testThrowsExceptionIfValidatedValueIsNotAttributeValue(): void
    {
        /** @var DateTime&MockObject $badObjectMock */
        $badObjectMock = $this->createMock(DateTime::class);
        /** @var ValidAttributeValue&MockObject $attributeValueConstraintMock */
        $attributeValueConstraintMock = $this->createMock(ValidAttributeValue::class);

        self::expectException(UnexpectedTypeException::class);
        self::expectExceptionMessage(
            'Expected argument of type "Sylius\Component\Attribute\Model\AttributeValueInterface", "string" given.',
        );

        $this->validAttributeValueValidator->validate($badObjectMock, $attributeValueConstraintMock);
    }
}
