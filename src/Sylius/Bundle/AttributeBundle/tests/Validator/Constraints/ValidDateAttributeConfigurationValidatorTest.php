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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidDateAttributeConfiguration;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidDateAttributeConfigurationValidator;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfiguration;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[AllowMockObjectsWithoutExpectations]
final class ValidDateAttributeConfigurationValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $context;

    private ValidDateAttributeConfigurationValidator $validator;

    private AttributeInterface&MockObject $attributeMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ValidDateAttributeConfigurationValidator();
        $this->attributeMock = $this->createMock(AttributeInterface::class);
        $this->validator->initialize($this->context);
    }

    #[DataProvider('dateAttributeTypesProvider')]
    public function testAddsAViolationIfFormatIsNotSupportedByTheIntlDateFormatter(string $type): void
    {
        $this->attributeMock->expects(self::once())->method('getType')->willReturn($type);
        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn(['format' => 'Y-m-d']);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->expects(self::once())->method('setParameter')->willReturn($violationBuilder);
        $violationBuilder->expects(self::once())->method('atPath')->with('configuration[format]')->willReturn($violationBuilder);
        $violationBuilder->expects(self::once())->method('addViolation');

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with('sylius.attribute.configuration.format.invalid')
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate($this->attributeMock, new ValidDateAttributeConfiguration());
    }

    #[DataProvider('dateAttributeTypesProvider')]
    public function testDoesNothingIfFormatIsNotSet(string $type): void
    {
        $this->attributeMock->expects(self::once())->method('getType')->willReturn($type);
        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn([]);

        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->attributeMock, new ValidDateAttributeConfiguration());
    }

    #[DataProvider('dateAttributeTypesProvider')]
    public function testDoesNothingIfFormatIsBlank(string $type): void
    {
        $this->attributeMock->expects(self::once())->method('getType')->willReturn($type);
        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn(['format' => '']);

        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->attributeMock, new ValidDateAttributeConfiguration());
    }

    #[DataProvider('availableFormatsProvider')]
    public function testDoesNothingIfFormatIsSupportedByTheIntlDateFormatter(string $format): void
    {
        $this->attributeMock->expects(self::once())->method('getType')->willReturn(DateAttributeType::TYPE);
        $this->attributeMock->expects(self::atLeastOnce())->method('getConfiguration')->willReturn(['format' => $format]);

        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->attributeMock, new ValidDateAttributeConfiguration());
    }

    public function testDoesNothingIfAnAttributeIsNeitherADateNorADatetimeType(): void
    {
        $this->attributeMock->expects(self::once())->method('getType')->willReturn(TextAttributeType::TYPE);

        $this->context->expects(self::never())->method('buildViolation');

        $this->validator->validate($this->attributeMock, new ValidDateAttributeConfiguration());
    }

    public function testThrowsAnExceptionIfValidatedValueIsNotAnAttribute(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate('badObject', new ValidDateAttributeConfiguration());
    }

    public function testThrowsAnExceptionIfConstraintIsNotAValidDateAttributeConfigurationConstraint(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $this->validator->validate($this->attributeMock, new ValidTextAttributeConfiguration());
    }

    public static function dateAttributeTypesProvider(): iterable
    {
        yield 'date' => [DateAttributeType::TYPE];
        yield 'datetime' => [DatetimeAttributeType::TYPE];
    }

    public static function availableFormatsProvider(): iterable
    {
        foreach (ValidDateAttributeConfiguration::AVAILABLE_FORMATS as $format) {
            yield $format => [$format];
        }
    }
}
