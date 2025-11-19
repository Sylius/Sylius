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

namespace Tests\Sylius\Bundle\ShippingBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Validator\Constraint\ValidDeliveryTimeRange;
use Sylius\Bundle\ShippingBundle\Validator\ValidDeliveryTimeRangeValidator;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ValidDeliveryTimeRangeValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private ValidDeliveryTimeRangeValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ValidDeliveryTimeRangeValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testThrowsWhenConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);

        $this->validator->validate($method, $constraint);
    }

    public function testThrowsWhenValueIsNotShippingMethod(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new ValidDeliveryTimeRange());
    }

    public function testDoesNotAddViolationWhenBothValuesAreNull(): void
    {
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(null);
        $method->method('getMaxDeliveryTimeDays')->willReturn(null);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($method, new ValidDeliveryTimeRange());
    }

    public function testDoesNotAddViolationWhenMaxIsGreaterOrEqualMin(): void
    {
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(3);
        $method->method('getMaxDeliveryTimeDays')->willReturn(3);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($method, new ValidDeliveryTimeRange());
    }

    public function testAddsViolationWhenMaxIsLowerThanMin(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        /** @var ShippingMethodInterface&MockObject $method */
        $method = $this->createMock(ShippingMethodInterface::class);
        $method->method('getMinDeliveryTimeDays')->willReturn(5);
        $method->method('getMaxDeliveryTimeDays')->willReturn(3);

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with((new ValidDeliveryTimeRange())->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('atPath')->with('maxDeliveryTimeDays')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($method, new ValidDeliveryTimeRange());
    }
}
