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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeType;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeTypeValidator;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AttributeTypeValidatorTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $attributeTypeRegistry;

    private ExecutionContextInterface&MockObject $contextMock;

    private AttributeTypeValidator $attributeTypeValidator;

    private AttributeInterface&MockObject $attribute;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attributeTypeRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->contextMock = $this->createMock(ExecutionContextInterface::class);
        $this->attribute = $this->createMock(AttributeInterface::class);
        $this->attributeTypeValidator = new AttributeTypeValidator($this->attributeTypeRegistry);
        $this->initialize($this->contextMock);
    }

    private function initialize(ExecutionContextInterface $context): void
    {
        $this->attributeTypeValidator->initialize($context);
    }

    public function testThrowsExceptionWhenValueIsNotAnAttribute(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $this->attributeTypeValidator->validate(new \stdClass(), new AttributeType());
    }

    public function testThrowsExceptionWhenConstraintIsNotAnAttributeType(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(\InvalidArgumentException::class);

        $this->attributeTypeValidator->validate($this->attribute, $constraint);
    }

    public function testDoesNothingWhenAttributeTypeIsNull(): void
    {
        $this->attribute->expects(self::once())->method('getType')->willReturn(null);

        $this->attributeTypeRegistry->expects(self::never())->method('has');

        $this->contextMock->expects(self::never())->method('addViolation');

        $this->attributeTypeValidator->validate($this->attribute, new AttributeType());
    }

    public function testDoesNothingWhenAttributeTypeIsRegistered(): void
    {
        $this->attribute->expects(self::once())->method('getType')->willReturn('foo');

        $this->attributeTypeRegistry->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(true);

        $this->contextMock->expects(self::never())->method('addViolation');

        $this->attributeTypeValidator->validate($this->attribute, new AttributeType());
    }

    public function testAddsViolationWhenAttributeTypeIsNotRegistered(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $constraint = new AttributeType();

        $this->attribute->expects(self::once())->method('getType')->willReturn('foo');

        $this->attributeTypeRegistry->expects(self::once())
            ->method('has')
            ->with('foo')
            ->willReturn(false);

        $this->attributeTypeRegistry->expects(self::once())
            ->method('all')
            ->willReturn(
                [
                    'foo_attribute_name' => 'foo_value',
                    'bar_attribute_name' => 'bar_value',
                ],
            );

        $this->contextMock->expects(self::once())
            ->method('buildViolation')
            ->with(
                $constraint->unregisteredAttributeTypeMessage,
                [
                    '%type%' => 'foo',
                    '%available_types%' => 'foo_attribute_name, bar_attribute_name',
                ],
            )
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('type')
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())->method('addViolation');

        $this->attributeTypeValidator->validate($this->attribute, $constraint);
    }
}
